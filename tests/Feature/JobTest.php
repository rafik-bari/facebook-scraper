<?php

namespace Tests\Feature;

use App\Jobs\ScrapePaginateKeyword;
use App\Keyword;
use Tests\TestCase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\RefreshDatabase;
use \Facebook\Facebook;

class JobTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testPushKeywordsToQueue()
    {
        Queue::fake();

        $appToken = \App\AppToken::first();
        $this->assertTrue(isset($appToken->app_id) && isset($appToken->app_secret));
        $app_id = $appToken->app_id;
        $app_secret = $appToken->app_secret;

        $keywords = ['test'];
        foreach (array_chunk($keywords, 49) as $keywords_chunk) {

            $kw = new Keyword();
            $kw->keywords_chunk = base64_encode(serialize($keywords_chunk));
            if ($kw->save()) {
                // create a job to process this keyword
                ScrapePaginateKeyword::dispatch($kw);
            }
            // Perform order shipping...

            Queue::assertPushed(ScrapePaginateKeyword::class);
        }

    }


    public function testScrapePaginateJob()
    {
        $kx = new Keyword();
        $kx->keywords_chunk = base64_encode(serialize(['test']));
        $job = new ScrapePaginateKeyword($kx);
        $job->handle();

        $this->assertTrue(count($job->responses) > 0);

        foreach ($job->responses as $response) {
            $this->assertTrue(!$response->isError());
        }


    }

}
