<?php

namespace App\Providers;
use App\ApiError;
use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Queue::failing(function (JobFailed $event) {
            $error = new ApiError();

            $error->message = 'Job:'.$event->job.'|'.$event->exception;
            $error->save();
            // $event->connectionName
            // $event->job
            // $event->exception
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
