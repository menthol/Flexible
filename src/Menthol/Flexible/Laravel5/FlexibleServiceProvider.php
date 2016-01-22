<?php namespace Menthol\Flexible\Laravel5;

use Illuminate\Support\ServiceProvider;
use Menthol\Flexible\Commands\PathsCommand;
use Menthol\Flexible\Commands\ReindexCommand;

class FlexibleServiceProvider extends ServiceProvider
{
    /**
     * FlexibleServiceProviderLaravel4 constructor.
     */
    public function __construct($app)
    {
        parent::__construct($app);

        require_once __DIR__ . '/../../../compatibility/laravel5.php';
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCommands();
        $this->registerBindings();
        $this->registerConfigs();
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
            $configPath = config_path() . '/config/flexible.php';
            $publishConfigCallable = function($command) {
                $command->call('vendor:publish', ['--provider' => 'Menthol\\Flexible\FlexibleServiceProvider', '--tag' => 'config']);
            };
            return new PathsCommand($configPath, $publishConfigCallable);
        });

        $this->commands('menthol.flexible.commands.reindex');
        $this->commands('menthol.flexible.commands.paths');
    }

    protected function registerBindings()
    {
        $this->app->singleton('flexible.config', function ($app) {
            return new Config($app);
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
