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
        $settingsRow = \App\Settings::findOrFail(1);
        $must_have_email = (bool)$settingsRow->must_have_email;
        $minimum_fans = intval($settingsRow->minimum_fan_count);
        if (isset($resp['data'])) {
            foreach ($resp['data'] as $page) {
                if ($must_have_email) {
                    if (!isset($page['emails']) || isset($page['emails']) && !count($page['emails'])) {
                        continue;
                    }
                }
                if ($page['fan_count'] < $minimum_fans) {
                    continue;
                }
                StorePageData::dispatch($page);
            }
            if (isset($resp['paging']) && isset($resp['paging']['next'])) {
                $next_url = $resp['paging']['next'];
                ScrapeNextPage::dispatch($next_url);
            }
        }
    }
}
