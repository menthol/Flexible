<?php namespace Menthol\Flexible;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Elasticsearch\Transport;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Menthol\Flexible\Response\Result;
use Monolog\Handler\NullHandler;
use Monolog\Logger;
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

    public function bootContainerBindings() {
        $this->bindElasticsearch();
        $this->bindLogger();
        $this->bindIndex();
        $this->bindQuery();
        $this->bindProxy();
        $this->bindResult();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->provider->register();
    }

    /**
     * Return the current laravel version
     *
     * @return int
     */
    protected function getLaravelVersion()
    {
        $app = get_class($this->app);
        if (defined($app.'::VERSION')) {
            return (int)constant($app.'::VERSION');
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
            $config = $app->make('flexible.config')->get('elasticsearch.params', []);
            return ClientBuilder::fromConfig($config);
        });
    }

    /**
     * Bind a Flexible log handler to the container
     */
    protected function bindLogger()
    {
        $this->app->singleton('menthol.flexible.logger', function ($app) {
            return new Logger('flexible', [new NullHandler()]);
        });
    }

    /**
     * Bind the Flexible index to the container
     */
    protected function bindIndex()
    {
        $this->app->bind('menthol.flexible.index', function ($app, $params) {
            $name = isset($params['name']) ? $params['name'] : '';

            return new Index($params['proxy'], $name);
        });
    }

    /**
     * Bind the Flexible Query to the container
     */
    protected function bindQuery()
    {
        $this->app->bind('menthol.flexible.query', function ($app, $params) {
            return new Query($params['proxy'], $params['term'], $params['options']);
        });
    }

    /**
     * Bind the Flexible proxy to the container
     */
    protected function bindProxy()
    {
        $this->app->bind('menthol.flexible.proxy', function ($app, $model) {
            return new Proxy($model);
        });
    }

    /**
     * Bind the Flexible result to the container
     */
    protected function bindResult()
    {
        $this->app->bind('menthol.flexible.response.result', function ($app, array $hit) {
            return new Result($hit);
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
