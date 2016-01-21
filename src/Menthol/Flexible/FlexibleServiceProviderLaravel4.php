<?php namespace Menthol\Flexible;

use Illuminate\Support\ServiceProvider;
use Menthol\Flexible\Commands\PathsCommand;
use Menthol\Flexible\Commands\ReindexCommand;

class FlexibleServiceProviderLaravel4 extends ServiceProvider
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

        $this->package('menthol/flexible', 'flexible', __DIR__ . '/../..');
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
            $configPath = app_path() . '/config/packages/menthol/flexible/config.php';
            $publishConfigCallable = function($command) {
                $command->call('config:publish', ['package' => 'menthol/flexible']);
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
