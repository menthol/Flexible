<?php namespace Menthol\Flexible;

use Illuminate\Support\Facades\App;
use Mockery as m;

/**
 * Class FlexibleServiceProviderTest
 * @group laravel4
 */
class FlexibleServiceProviderTest extends \PHPUnit_Framework_TestCase
{

    public static $functions;
    protected static $providers_real_path;

    /**
     * @test
     */
    public function it_should_bind_elasticsearch()
    {
        /**
         * Set
         */
        $app = m::mock('LaravelApp');
        $config = m::mock('Menthol\Flexible\Config');
        $sp = m::mock('Menthol\Flexible\FlexibleServiceProvider[bindElasticsearch]', [$app]);

        /**
         * Expectation
         */

        $app->shouldReceive('make')
            ->with('Menthol\Flexible\Config')
            ->once()
            ->andReturn($config);

        $config->shouldReceive('get')
            ->with('elasticsearch.params', [])
            ->andReturn([]);

        $app->shouldReceive('singleton')
            ->once()
            ->andReturnUsing(
                function ($name, $closure) use ($app) {
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
        $sp = m::mock('Menthol\Flexible\FlexibleServiceProvider[bindLogger]', [$app]);

        /**
         * Expectation
         */
        $app->shouldReceive('singleton')
            ->once()
            ->andReturnUsing(
                function ($name, $closure) use ($app) {
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

        App::shouldReceive('make')
            ->with('menthol.flexible.index', m::any())
            ->once()
            ->andReturn('mock');

        App::shouldReceive('make')
            ->with('Elasticsearch')
            ->twice()
            ->andReturn('mock');

        $config = m::mock('Menthol\Flexible\Config');

        App::shouldReceive('make')
            ->with('Menthol\Flexible\Config')
            ->once()
            ->andReturn($config);

        $config->shouldReceive('get')
            ->with('elasticsearch.index_prefix', '')
            ->andReturn('');

        $model = m::mock('Illuminate\Database\Eloquent\Model');
        $model->shouldReceive('getTable')
            ->once()
            ->andReturn('mockType');
        $app = m::mock('LaravelApp');
        $proxy = m::mock('Menthol\Flexible\Proxy', [$model]);
        $sp = m::mock('Menthol\Flexible\FlexibleServiceProvider[bindIndex]', [$app]);

        /**
         * Expectation
         */
        $app->shouldReceive('bind')
            ->once()
            ->andReturnUsing(
                function ($name, $closure) use ($app, $proxy) {
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
        $sp = m::mock('Menthol\Flexible\FlexibleServiceProvider[bindQuery]', [$app]);

        /**
         * Expectation
         */
        $app->shouldReceive('bind')
            ->once()
            ->andReturnUsing(
                function ($name, $closure) use ($app, $proxy) {
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
        $sp = m::mock('Menthol\Flexible\FlexibleServiceProvider[bindProxy]', [$app]);

        /**
         * Expectation
         */
        $app->shouldReceive('bind')
            ->once()
            ->andReturnUsing(
                function ($name, $closure) use ($app, $model) {
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
        $sp = m::mock('Menthol\Flexible\FlexibleServiceProvider[bindResult]', [$app]);

        /**
         * Expectation
         */
        $app->shouldReceive('bind')
            ->once()
            ->andReturnUsing(
                function ($name, $closure) use ($app) {
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

    protected function setUp()
    {
        self::$functions = m::mock();
        self::$functions->shouldReceive('base_path')->andReturn('');
        self::$providers_real_path = realpath(__DIR__ . '/../../../src/Menthol/Flexible');
    }

    protected function tearDown()
    {
        m::close();
    }
}
