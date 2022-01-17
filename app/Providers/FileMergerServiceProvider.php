<?php

namespace App\Providers;

use App\Service\FileMerger;
use Illuminate\Support\ServiceProvider;

class FileMergerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton('FileMerger', function () {
            return new FileMerger();
        });
    }
}
