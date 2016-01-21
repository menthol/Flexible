<?php namespace Menthol\Flexible;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Mockery as m;

function base_path($path = null)
{
    return FlexibleServiceProviderTest::$functions->base_path($path);
}

function app_path($path = null)
{
    return FlexibleServiceProviderTest::$functions->app_path($path);
}

function config_path($config_path = null)
{
    return FlexibleServiceProviderTest::$functions->config_path($config_path);
}

/**
 * Class FlexibleServiceProviderTest
 */
class FlexibleServiceProviderTest extends \PHPUnit_Framework_TestCase {

    public static $functions;
    protected static $providers_real_path;

    protected function setup()
    {
        self::$functions = m::mock();
        self::$functions->shouldReceive('base_path')->andReturn('');
        self::$providers_real_path = realpath(__DIR__ . '/../../../src/Menthol/Flexible');
    }

    protected function tearDown()
    {
        m::close();
    }

    /**
     * @test
     * @group laravel5
     */
    public function it_should_boot_on_laravel5()
    {
        /**
         * Set
         */
        $sp = m::mock('Menthol\Flexible\Laravel5\FlexibleServiceProvider[bootContainerBindings, publishes]', ['something']);
        $sp->shouldAllowMockingProtectedMethods();

        /**
         * Expectation
         */
        $sp->shouldReceive('publishes')
            ->with([
                self::$providers_real_path . '/../../config/flexible.php' => config_path('flexible.php'),
            ], 'config')
            ->once();

        $sp->shouldReceive('bootContainerBindings')
            ->once();

        /**
         * Assertion
         */
        $sp->boot();
    }

    /**
     * @test
     * @group laravel5
     */
    public function it_should_boot_container_bindings_on_laravel5()
    {
        /**
         * Set
         */
        $sp = m::mock('Menthol\Flexible\Laravel5\FlexibleServiceProvider[' .
            'bindProxy, bindIndex, bindLogger, bindElasticsearch, bindQuery, bindResult]', ['something']);
        $sp->shouldAllowMockingProtectedMethods();

        /**
         * Expectation
         */
        $sp->shouldReceive('bindElasticsearch')->once()->andReturn(true);
        $sp->shouldReceive('bindLogger')->once()->andReturn(true);
        $sp->shouldReceive('bindProxy')->once()->andReturn(true);
        $sp->shouldReceive('bindIndex')->once()->andReturn(true);
        $sp->shouldReceive('bindQuery')->once()->andReturn(true);
        $sp->shouldReceive('bindResult')->once()->andReturn(true);

        /**
         * Assertions
         */
        $sp->bootContainerBindings();
    }

    /**
     * @test
     * @group laravel5
     */
    public function it_should_bind_elasticsearch_on_laravel5()
    {
        /**
         * Set
         */
        $app = m::mock('LaravelApp');
        $sp = m::mock('Menthol\Flexible\Laravel5\FlexibleServiceProvider[bindElasticsearch]', [$app]);

        /**
         * Expectation
         */
        Config::shouldReceive('get')
            ->with('flexible.elasticsearch.params')
            ->once()
            ->andReturn([]);

        $app->shouldReceive('singleton')
            ->once()
            ->andReturnUsing(
                function ($name, $closure) use ($app)
                {
                    $this->assertEquals('Elasticsearch', $name);
                    $this->assertInstanceOf('Elasticsearch\Client', $closure($app));
                }
            );

        $sp->bindElasticsearch();
    }

    /**
     * @test
     * @group laravel5
     */
    public function it_should_bind_logger_on_laravel5()
    {
        /**
         * Set
         */
        $app = m::mock('LaravelApp');
        $sp = m::mock('Menthol\Flexible\Laravel5\FlexibleServiceProvider[bindLogger]', [$app]);

        /**
         * Expectation
         */
        $app->shouldReceive('singleton')
            ->once()
            ->andReturnUsing(
                function ($name, $closure) use ($app)
                {
                    $this->assertEquals('menthol.flexible.logger', $name);
                    $this->assertInstanceOf('Monolog\Logger', $closure($app));
                }
            );

        $sp->bindLogger();
    }

    /**
     * @test
     * @group laravel5
     */
    public function it_should_bind_index_on_laravel5()
    {
        /**
         * Set
         */
        App::clearResolvedInstances();
        Config::clearResolvedInstances();

        App::shouldReceive('make')
            ->with('menthol.flexible.index', m::any())
            ->once()
            ->andReturn('mock');

        App::shouldReceive('make')
            ->with('Elasticsearch')
            ->twice()
            ->andReturn('mock');

        Config::shouldReceive('get')
            ->with('flexible.elasticsearch.index_prefix', '')
            ->andReturn('');

        $model = m::mock('Illuminate\Database\Eloquent\Model');
        $model->shouldReceive('getTable')
            ->once()
            ->andReturn('mockType');
        $app = m::mock('LaravelApp');
        $proxy = m::mock('Menthol\Flexible\Proxy', [$model]);
        $sp = m::mock('Menthol\Flexible\Laravel5\FlexibleServiceProvider[bindIndex]', [$app]);

        /**
         * Expectation
         */
        $app->shouldReceive('bind')
            ->once()
            ->andReturnUsing(
                function ($name, $closure) use ($app, $proxy)
                {
                    $this->assertEquals('menthol.flexible.index', $name);
                    $this->assertInstanceOf('Menthol\Flexible\Index',
                        $closure($app, ['proxy' => $proxy, 'name' => 'name']));
                }
            );


        /**
         * Assertion
         */
        $sp->bindIndex();
    }

    /**
     * @test
     * @group laravel5
     */
    public function it_should_bind_query_on_laravel5()
    {
        /**
         * Set
         */
        $model = m::mock('Illuminate\Database\Eloquent\Model');
        $model->shouldReceive('getTable')
            ->once()
            ->andReturn('mockType');
        $app = m::mock('LaravelApp');
        $proxy = m::mock('Menthol\Flexible\Proxy', [$model]);
        $sp = m::mock('Menthol\Flexible\Laravel5\FlexibleServiceProvider[bindQuery]', [$app]);

        /**
         * Expectation
         */
        $app->shouldReceive('bind')
            ->once()
            ->andReturnUsing(
                function ($name, $closure) use ($app, $proxy)
                {
                    $this->assertEquals('menthol.flexible.query', $name);
                    $this->assertInstanceOf('Menthol\Flexible\Query',
                        $closure($app, ['proxy' => $proxy, 'term' => 'term', 'options' => []]));
                }
            );

        /**
         * Assertion
         */
        $sp->bindQuery();
    }

    /**
     * @test
     * @group laravel5
     */
    public function it_should_bind_proxy_on_laravel5()
    {
        /**
         * Set
         */
        $model = m::mock('Illuminate\Database\Eloquent\Model');
        $model->shouldReceive('getTable')
            ->once()
            ->andReturn('mockType');
        $app = m::mock('LaravelApp');
        $sp = m::mock('Menthol\Flexible\Laravel5\FlexibleServiceProvider[bindProxy]', [$app]);

        /**
         * Expectation
         */
        $app->shouldReceive('bind')
            ->once()
            ->andReturnUsing(
                function ($name, $closure) use ($app, $model)
                {
                    $this->assertEquals('menthol.flexible.proxy', $name);
                    $this->assertInstanceOf('Menthol\Flexible\Proxy',
                        $closure($app, $model));
                }
            );


        /**
         * Assertion
         */
        $sp->bindProxy();
    }

    /**
     * @test
     * @group laravel5
     */
    public function it_should_bind_result_on_laravel5()
    {
        /**
         * Set
         */
        $app = m::mock('LaravelApp');
        $sp = m::mock('Menthol\Flexible\Laravel5\FlexibleServiceProvider[bindResult]', [$app]);

        /**
         * Expectation
         */
        $app->shouldReceive('bind')
            ->once()
            ->andReturnUsing(
                function ($name, $closure) use ($app)
                {
                    $this->assertEquals('menthol.flexible.response.result', $name);
                    $this->assertInstanceOf('Menthol\Flexible\Response\Result',
                        $closure($app, []));
                }
            );

        /**
         * Assertion
         */
        $sp->bindResult();
    }

    /**
     * @test
     * @group laravel5
     */
    public function it_should_register_commands_on_laravel5()
    {
        /**
         * Set
         */
        $app = m::mock('Illuminate\Container\Container');
        $sp = m::mock('Menthol\Flexible\Laravel5\FlexibleServiceProvider[commands, mergeConfigFrom]', [$app]);
        $sp->shouldAllowMockingProtectedMethods();

        /**
         * Expectation
         */
        $app->shouldReceive('offsetSet')->andReturn(true);
        $app->shouldReceive('offsetGet')->andReturn(true);

        $app->shouldReceive('share')
            ->once()
            ->andReturnUsing(function ($closure) use ($app)
            {
                $this->assertInstanceOf('Menthol\Flexible\Commands\ReindexCommand', $closure($app));
            });

        $app->shouldReceive('share')
            ->once()
            ->andReturnUsing(function ($closure) use ($app)
            {
                $this->assertInstanceOf('Menthol\Flexible\Commands\PathsCommand', $closure($app));
            });

        $sp->shouldReceive('commands')
            ->with('menthol.flexible.commands.reindex')
            ->once()
            ->andReturn(true);

        $sp->shouldReceive('commands')
            ->with('menthol.flexible.commands.paths')
            ->once()
            ->andReturn(true);

        $sp->shouldReceive('mergeConfigFrom')
            ->with(self::$providers_real_path . '/../../config/flexible.php', 'flexible')
            ->once();

        /**
         * Assertion
         */
        $sp->register();
    }

    /**
     * @test
     * @group laravel5
     */
    public function it_should_provide_services_on_laravel5()
    {
        /**
         * Set
         */
        $app = m::mock('LaravelApp');

        /**
         * Assertion
         */
        $sp = new FlexibleServiceProvider($app);
        $this->assertEquals([], $sp->provides());
    }

}
