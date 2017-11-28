<?php

namespace Cherry\DirectMail;

use Illuminate\Mail\TransportManager;
use Illuminate\Support\ServiceProvider;

class DirectMailTransportProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(dirname(__DIR__).'/config/services.php', 'services');

        $this->app->resolving('swift.transport', function (TransportManager $tm) {
            $tm->extend('directmail', function () {
                $config = config('services.directmail');
                return new DirectMailTransport($config);
            });
        });
    }
}
