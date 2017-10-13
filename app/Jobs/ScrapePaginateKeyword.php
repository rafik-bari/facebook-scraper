<?php

namespace App\Jobs;

use App\Keyword;
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
    public function __construct(Keyword $keyword)
    {
        //
        $this->keywords_chunk = $keyword->keywords_chunk;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // require_once __DIR__ . '/vendor/autoload.php'; // change path as needed
        $app_id = '1083006501758342';
        $app_secret = '73c47ebeba31ae98dc00dd7d152f2a86';
        $fb = new \Facebook\Facebook([
            'app_id' => $app_id,
            'app_secret' => $app_secret,
            'default_graph_version' => 'v2.10',
        ]);


        $access_token = $app_id . '|' . $app_secret;
        $fields = \App\PageField::where('enabled', '1')->get(['name'])->implode('name', ',');
        $pages_ids = \App\PendingPage::all();
        $batch = [];
        $fb->setDefaultAccessToken($access_token);

        foreach (unserialize($this->keywords_chunk) as $keyword) {
            $keyword_ = urlencode($keyword);
            // $batch[] = $fb->request('GET',"/$page->page_id?fields=$fields");
            $batch[] = $fb->request('GET',"/search?type=page&q=$keyword_&fields=$fields&limit=5000");
        }


        try {
            $responses = $fb->sendBatchRequest($batch);
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            $apiErrpr = new \App\ApiError();
            $apiErrpr->e = serialize($e);
            $apiErrpr->message =  $e->getMessage();
            $apiErrpr->response = serialize($e->getResponse());
            $apiErrpr->save();
            exit;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            $apiErrpr = new \App\ApiError();
            $apiErrpr->e = serialize($e);
            $apiErrpr->message =  $e->getMessage();
            $apiErrpr->response = serialize($e->getResponse());
            $apiErrpr->save();
            exit;
        }

        foreach ($responses as $key => $response) {
            if ($response->isError()) {
                $apiErrpr = new \App\ApiError();
                $e = $response->getThrownException();
                $apiErrpr->e = serialize($e);
                $apiErrpr->message =  $e->getMessage();
                $apiErrpr->response = serialize($e->getResponse());
                $apiErrpr->key = serialize($key);
                $apiErrpr->save();

            } else {
                $resp = $response->getDecodedBody();
                $pages_data = $resp['data'];
                $chunk_100_pages=[];
                foreach (array_chunk($pages_data,100) as $pages_rows) {
                    $chunk_100_pages[] = [
                        'pages_data_chunk'=>serialize($pages_rows),
                        'chunk_size' => count($pages_rows)
                    ];
                    \DB::table('pages')->insert($chunk_100_pages);
                }
            }
        }


    }
}
