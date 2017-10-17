<?php

namespace App\Jobs;

use App\Keyword;
use App\Page;
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

        foreach (unserialize($this->keywords_chunk) as $keyword) {
            $keyword_ = urlencode($keyword);
            // $batch[] = $fb->request('GET',"/$page->page_id?fields=$fields");
            $batch[] = $fb->request('GET', "/search?type=page&q=$keyword_&fields=$fields&limit=100");
        }


        try {
            $responses = $fb->sendBatchRequest($batch);
        } catch (Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            $apiErrpr = new \App\ApiError();
            $apiErrpr->message = $e->getMessage();
            $apiErrpr->save();
            exit;
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            $apiErrpr = new \App\ApiError();
            $apiErrpr->message = $e->getMessage();
            $apiErrpr->save();
            exit;
        }

        foreach ($responses as $key => $response) {
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
                    \App\Jobs\StorePageData::dispatch($page);
                }
                if(isset($resp['paging']) && isset($resp['paging']['next'])) {
                    $next_url = $resp['paging']['next'];
                    \App\Jobs\ScrapeNextPage::dispatch($next_url);
                }
            }
        }


    }
}
