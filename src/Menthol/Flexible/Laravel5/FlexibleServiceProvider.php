<?php namespace Menthol\Flexible\Laravel5;

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
        $this->app->singleton('Menthol\Flexible\Config', function ($app) {
            return new Config($app, 'flexible.');
        });
    }

    protected function registerConfigs()
    {
        if (file_exists(config_path('flexible.php'))) {
            $this->mergeConfigFrom(config_path('flexible.php'), 'flexible');
        } else {
            $this->mergeConfigFrom(__DIR__ . '/../../../config/flexible.php', 'flexible');
        }
        $this->publishes([
            __DIR__ . '/../../../config/flexible.php' => config_path('flexible.php'),
        ]);
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
