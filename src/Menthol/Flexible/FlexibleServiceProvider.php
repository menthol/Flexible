<?php namespace Menthol\Flexible;

use Elasticsearch\ClientBuilder;
use Illuminate\Support\ServiceProvider;
use Menthol\Flexible\Commands\ReindexCommand;
use Menthol\Flexible\Response\Result;
use RuntimeException;

class FlexibleServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * The actual provider
     *
     * @var \Illuminate\Support\ServiceProvider
     */
    protected $provider;

    /**
     * Construct the Flexible service provider
     */
    public function __construct($app)
    {
        parent::__construct($app);

        $this->provider = $this->getProvider();
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->bootContainerBindings();
        $this->provider->boot();
    }

    public function bootContainerBindings()
    {
        $this->bindElasticsearch();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCommands();
        $this->provider->register();
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

        $this->commands('menthol.flexible.commands.reindex');
    }

    /**
     * Return the current laravel version
     *
     * @return int
     */
    protected function getLaravelVersion()
    {
        $app = get_class($this->app);
        if (defined($app . '::VERSION')) {
            return (int)constant($app . '::VERSION');
        }
        return method_exists($this, 'package') ? 4 : 5;
    }

    /**
     * Return the service provider for the particular Laravel version
     *
     * @return mixed
     */
    private function getProvider()
    {
        $app = $this->app;
        switch ($this->getLaravelVersion()) {
            case 4:
                return new Laravel4\FlexibleServiceProvider($app);
            case 5:
                return new Laravel5\FlexibleServiceProvider($app);
            default:
                throw new RuntimeException('Your version of Laravel is not supported');
        }
    }

    /**
     * Bind the Elasticsearch client to the container
     */
    protected function bindElasticsearch()
    {
        $this->app->singleton('Elasticsearch', function ($app) {
            $config = $app->make('Menthol\Flexible\Config')->get('elasticsearch.params', []);
            return ClientBuilder::fromConfig($config);
        });
    }

    /**
     * Catch dynamic method calls intended for the real provider
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->provider, $method], $parameters);
    }
}
