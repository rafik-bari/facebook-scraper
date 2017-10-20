<?php

namespace App\Jobs;

use App\Keyword;
use App\Page;
use App\ScrapedKeyword;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ScrapePaginateKeyword implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $keywords_chunk;
    public $responses;

    public function __construct(Keyword $kw)
    {
        //

        $this->keywords_chunk = base64_decode($kw->keywords_chunk);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // require_once __DIR__ . '/vendor/autoload.php'; // change path as needed
        $appToken = \App\AppToken::first();
        $app_id = $appToken->app_id;
        $app_secret = $appToken->app_secret;
        $fb = new \Facebook\Facebook([
            'app_id' => $app_id,
            'app_secret' => $app_secret,
            'default_graph_version' => 'v2.10',
        ]);


        $access_token = $app_id . '|' . $app_secret;
        $fields = \App\PageField::get(['name'])->implode('name', ',');
        $pages_ids = \App\PendingPage::all();
        $batch = [];
        $fb->setDefaultAccessToken($access_token);

        try {
            foreach (unserialize($this->keywords_chunk) as $keyword) {
                $keyword_ = urlencode($keyword);
                // $batch[] = $fb->request('GET',"/$page->page_id?fields=$fields");
                $batch[] = $fb->request('GET', "/search?type=page&q=$keyword_&fields=$fields&limit=100");
            }
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            $apiErrpr = new \App\ApiError();
            $apiErrpr->message = $e->getMessage();
            $apiErrpr->save();
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            $apiErrpr = new \App\ApiError();
            $apiErrpr->message = $e->getMessage();
            $apiErrpr->save();
            exit;
        }


        try {
            $responses = $fb->sendBatchRequest($batch);
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            $apiErrpr = new \App\ApiError();
            $apiErrpr->message = $e->getMessage();
            $apiErrpr->save();
            exit;
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            $apiErrpr = new \App\ApiError();
            $apiErrpr->message = $e->getMessage();
            $apiErrpr->save();
            exit;
        }

        $settingsRow = \App\Settings::findOrFail(1);
        $must_have_email = (bool)$settingsRow->must_have_email;
        $minimum_fans = intval($settingsRow->minimum_fan_count);
        foreach ($responses as $key => $response) {
            $this->responses = $responses;


            if ($response->isError()) {

                $apiErrpr = new \App\ApiError();
                $e = $response->getThrownException();

                $apiErrpr->message = $e->getMessage();


                $apiErrpr->save();

            } else {

                $resp = $response->getDecodedBody();
                $pages_data = $resp['data'];
                $chunk_100_pages = [];
                foreach ($pages_data as $page) {
                    if ($must_have_email) {
                        if (!isset($page['emails']) || isset($page['emails']) && !count($page['emails'])) {
                            continue;
                        }
                    }
                    if ($page['fan_count'] < $minimum_fans) {
                        continue;
                    }

                    \App\Jobs\StorePageData::dispatch($page);
                }
                if (isset($resp['paging']) && isset($resp['paging']['next'])) {
                    $next_url = $resp['paging']['next'];
                    \App\Jobs\ScrapeNextPage::dispatch($next_url);
                }
            }
        }


    }
}
