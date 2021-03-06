<?php namespace Menthol\Flexible\Laravel4;

use Illuminate\Support\ServiceProvider;
use Menthol\Flexible\Commands\PathsCommand;
use Menthol\Flexible\Commands\ReindexCommand;
use Menthol\Flexible\Config;

class FlexibleServiceProvider extends ServiceProvider
{
    /**
     * FlexibleServiceProviderLaravel4 constructor.
     */
    public function __construct($app)
    {
        parent::__construct($app);

        require_once __DIR__ . '/../../../compatibility/laravel4.php';
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
            $configPath = app_path() . '/config/packages/menthol/flexible/flexible.php';
            $publishConfigCallable = function ($command) {
                $command->call('config:publish', ['package' => 'menthol/flexible']);
            };
            return new PathsCommand($configPath, $publishConfigCallable);
        });

        $this->commands('menthol.flexible.commands.reindex');
        $this->commands('menthol.flexible.commands.paths');
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
