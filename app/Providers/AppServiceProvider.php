<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Channel;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \View::composer('*',function($view){
            $channel = \Cache::rememberForever('channels',function(){
                return Channel::all();
            });
            $view->with('channels',$channel);
        });
    }
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if($this->app->isLocal()){
            $this->app->register(\Barryvdh\Debugbar\ServiceProvider::class);
        }
    }
}
