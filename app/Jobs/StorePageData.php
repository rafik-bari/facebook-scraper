<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class StorePageData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $page_data;
    public function __construct($page)
    {
        //
        $this->page_data = $page;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $pa = new \App\Page();

        foreach ($this->page_data as $k => $v) {
            if (is_array($v)) {
                $this->page_data[$k] = serialize($v);
            }
        }
        $pa->fill($this->page_data);
        $pa->save();
    }
}
