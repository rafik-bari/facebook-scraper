<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ScrapeNextPage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $scrappable_url;

    public function __construct($scrappable_url)
    {
        //
        $this->scrappable_url = $scrappable_url;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $ch = curl_init();
        // Disable SSL verification
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // Will return the response, if false it print the response
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Set the url
        curl_setopt($ch, CURLOPT_URL, $this->scrappable_url);
        // Execute
        $result = curl_exec($ch);
        // Closing
        curl_close($ch);

        // Will dump a beauty json :3
        $resp = json_decode($result, true);
        if (isset($resp['data'])) {
            foreach ($resp['data'] as $page) {
                StorePageData::dispatch($page);
            }
            if (isset($resp['paging']) && isset($resp['paging']['next'])) {
                $next_url = $resp['paging']['next'];
                ScrapeNextPage::dispatch($next_url);
            }
        }
    }
}
