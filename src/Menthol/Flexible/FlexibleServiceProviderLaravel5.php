<?php namespace Menthol\Flexible;

use Illuminate\Support\ServiceProvider;
use Menthol\Flexible\Commands\PathsCommand;
use Menthol\Flexible\Commands\ReindexCommand;

class FlexibleServiceProviderLaravel5 extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCommands();

        if (file_exists(config_path('flexible.php'))) {
            $this->mergeConfigFrom(config_path('flexible.php'), 'flexible');
        } else {
            $this->mergeConfigFrom(__DIR__ . '/../../config/flexible.php', 'flexible');
        }
        $this->publishes([
            __DIR__ . '/../../config/flexible.php' => config_path('flexible.php'),
        ]);
    }

    /**
     * Register the commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        $this->app['menthol.flexible.commands.reindex'] = $this->app->share(function ($app) {
            return new ReindexCommand();
        });

        $this->app['menthol.flexible.commands.paths'] = $this->app->share(function ($app) {
            $configPath = base_path() . '/config/flexible.php';
            $publishConfigCallable = function($command) {
                $command->call('vendor:publish', ['--provider' => 'Menthol\\Flexible\FlexibleServiceProvider', '--tag' => 'config']);
            };
            return new PathsCommand($configPath, $publishConfigCallable);
        });

        $this->commands('menthol.flexible.commands.reindex');
        $this->commands('menthol.flexible.commands.paths');
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
