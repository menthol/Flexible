<?php namespace Menthol\Flexible;

use Elasticsearch\Client;
use Illuminate\Support\ServiceProvider;
use Menthol\Flexible\Commands\PathsCommand;
use Menthol\Flexible\Commands\ReindexCommand;
use Menthol\Flexible\Response\Result;
use Monolog\Handler\NullHandler;
use Monolog\Logger;

class FlexibleServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    public function boot()
    {
        $this->bootContainerBindings();

        $this->publishes([
            __DIR__ . '/../../config/flexible.php' => base_path('config/flexible.php'),
        ], 'config');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCommands();

        if (file_exists(base_path('config/flexible.php')))
        {
            $this->mergeConfigFrom(base_path('config/flexible.php'), 'flexible');
        }
        else
        {
            $this->mergeConfigFrom(__DIR__ . '/../../config/flexible.php', 'flexible');
        }
    }

    /**
     * Boot the container bindings.
     *
     * @return void
     */
    public function bootContainerBindings()
    {
        $this->bindElasticsearch();
        $this->bindLogger();
        $this->bindIndex();
        $this->bindQuery();
        $this->bindProxy();
        $this->bindResult();
    }

    /**
     * Bind a Flexible log handler to the container
     */
    protected function bindLogger()
    {
        $this->app->singleton('menthol.flexible.logger', function ($app)
        {
            return new Logger('flexible', [new NullHandler()]);
        });
    }

    /**
     * Bind the Elasticsearch client to the container
     */
    protected function bindElasticsearch()
    {
        $this->app->singleton('Elasticsearch', function ($app)
        {
            return new Client(\Illuminate\Support\Facades\Config::get('flexible.elasticsearch.params'));
        });
    }

    /**
     * Bind the Flexible index to the container
     */
    protected function bindIndex()
    {
        $this->app->bind('menthol.flexible.index', function ($app, $params)
        {
            $name = isset($params['name']) ? $params['name'] : '';

            return new Index($params['proxy'], $name);
        });
    }

    /**
     * Bind the Flexible Query to the container
     */
    protected function bindQuery()
    {
        $this->app->bind('menthol.flexible.query', function ($app, $params)
        {
            return new Query($params['proxy'], $params['term'], $params['options']);
        });
    }

    /**
     * Bind the Flexible proxy to the container
     */
    protected function bindProxy()
    {
        $this->app->bind('menthol.flexible.proxy', function ($app, $model)
        {
            return new Proxy($model);
        });
    }

    /**
     * Bind the Flexible result to the container
     */
    protected function bindResult()
    {
        $this->app->bind('menthol.flexible.response.result', function ($app, array $hit)
        {
            return new Result($hit);
        });
    }

    /**
     * Register the commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        $this->app['menthol.flexible.commands.reindex'] = $this->app->share(function ($app)
        {
            return new ReindexCommand();
        });

        $this->app['menthol.flexible.commands.paths'] = $this->app->share(function ($app)
        {
            return new PathsCommand();
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
