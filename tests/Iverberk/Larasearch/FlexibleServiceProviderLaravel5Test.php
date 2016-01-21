<?php namespace Menthol\Flexible;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Mockery as m;

function base_path($path = null)
{
    return FlexibleServiceProviderLaravel5Test::$functions->base_path($path);
}

function config_path()
{
    return FlexibleServiceProviderLaravel5Test::$functions->config_path();
}

/**
 * Class FlexibleServiceProviderTest
 */
class FlexibleServiceProviderLaravel5Test extends \PHPUnit_Framework_TestCase {

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
     */
    public function it_should_boot()
    {
        /**
         * Set
         */
        $sp = m::mock('Menthol\Flexible\FlexibleServiceProviderLaravel5[bootContainerBindings, publishes]', ['something']);
        $sp->shouldAllowMockingProtectedMethods();

        /**
         * Expectation
         */
        $sp->shouldReceive('publishes')
            ->with([
                self::$providers_real_path . '/../../config/flexible.php' => base_path('config/flexible.php'),
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
     */
    public function it_should_boot_container_bindings()
    {
        /**
         * Set
         */
        $sp = m::mock('Menthol\Flexible\FlexibleServiceProviderLaravel5[' .
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
     */
    public function it_should_bind_elasticsearch()
    {
        /**
         * Set
         */
        $app = m::mock('LaravelApp');
        $sp = m::mock('Menthol\Flexible\FlexibleServiceProviderLaravel5[bindElasticsearch]', [$app]);

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
     */
    public function it_should_bind_logger()
    {
        /**
         * Set
         */
        $app = m::mock('LaravelApp');
        $sp = m::mock('Menthol\Flexible\FlexibleServiceProviderLaravel5[bindLogger]', [$app]);

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
     */
    public function it_should_bind_index()
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
        $sp = m::mock('Menthol\Flexible\FlexibleServiceProviderLaravel5[bindIndex]', [$app]);

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
     */
    public function it_should_bind_query()
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
        $sp = m::mock('Menthol\Flexible\FlexibleServiceProviderLaravel5[bindQuery]', [$app]);

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
     */
    public function it_should_bind_proxy()
    {
        /**
         * Set
         */
        $model = m::mock('Illuminate\Database\Eloquent\Model');
        $model->shouldReceive('getTable')
            ->once()
            ->andReturn('mockType');
        $app = m::mock('LaravelApp');
        $sp = m::mock('Menthol\Flexible\FlexibleServiceProviderLaravel5[bindProxy]', [$app]);

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
     */
    public function it_should_bind_result()
    {
        /**
         * Set
         */
        $app = m::mock('LaravelApp');
        $sp = m::mock('Menthol\Flexible\FlexibleServiceProviderLaravel5[bindResult]', [$app]);

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
     */
    public function it_should_register_commands()
    {
        /**
         * Set
         */
        $app = m::mock('Illuminate\Container\Container');
        $sp = m::mock('Menthol\Flexible\FlexibleServiceProviderLaravel5[commands, mergeConfigFrom]', [$app]);
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
     */
    public function it_should_provide_services()
    {
        /**
         * Set
         */
        $app = m::mock('LaravelApp');

        /**
         * Assertion
         */
        $sp = new FlexibleServiceProviderLaravel5($app);
        $this->assertEquals([], $sp->provides());
    }

}
