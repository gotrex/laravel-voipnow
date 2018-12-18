<?php

namespace Gotrex\VoipNow;

use Illuminate\Container\Container;
use Illuminate\Support\ServiceProvider;
use Gotrex\VoipNow\Adapter\SoapClientAdapter;

class VoipNowServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/voipnow.php' => config_path('voipnow.php'),
            ], 'config');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/voipnow.php', 'voipnow');

        $this->registerClient();

        $this->registerVoipNow();
    }

    public function registerClient()
    {
        $this->app->singleton('voipnow.client', function () {
            return new SoapClientAdapter();
        });

        $this->app->alias('voipnow.client', SoapClientAdapter::class);
    }

    public function registerVoipNow()
    {
        $this->app->singleton('voipnow', function (Container $app) {
            $config = $app['config'];
            $client = $app['voipnow.client'];

            return new VoipNowClass($config, $client);
        });

        $this->app->alias('voipnow', VoipNowFacade::class);
    }
}
