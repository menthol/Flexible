<?php namespace Menthol\Flexible\Laravel4;

use Illuminate\Support\ServiceProvider;
use Menthol\Flexible\Commands\PathsCommand;
use Menthol\Flexible\Commands\ReindexCommand;
use Menthol\Flexible\Config;

class FlexibleServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerBindings();
        $this->registerConfigs();
    }

    protected function registerBindings()
    {
        $app = $this->app;
        $this->app->singleton('Menthol\Flexible\Config', function () use ($app) {
            return new Config($app, 'flexible::flexible.');
        });
    }

    protected function registerConfigs()
    {
        $this->package('menthol/flexible', 'flexible', __DIR__ . '/../../..');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }

}
