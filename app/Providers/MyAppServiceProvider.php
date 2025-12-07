<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Models\SiteInfo as Setting;


use Log;

class MyAppServiceProvider extends ServiceProvider
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
        view()->composer('*', function ($view) {
            $view->with('pageGlobalData', $this->pageGlobalData());
        });
    }

    public function pageGlobalData(){
        $setting = Setting::first();

        $data = new \stdClass();
        $data->setting = $setting;

        return $data;
    }
}