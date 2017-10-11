<?php

namespace App\Jobs;

use App\PendingPage;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class FetchPageDetails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $page_id;

    /**
     * Create a new job instance.
     *
     * @param PendingPage $pendingPage
     */
    public function __construct(PendingPage $pendingPage)
    {
        $this->page_id = $pendingPage->id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Fetch the details of this page id

    }
}
