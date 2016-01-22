<?php namespace Menthol\Flexible\Laravel4;

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
 * @group laravel4
 */
class FlexibleServiceProviderTest extends \PHPUnit_Framework_TestCase {

    public static $functions;
    protected static $providers_real_path;

    protected function setUp()
    {
        self::$functions = m::mock();
        self::$functions->shouldReceive('base_path')->andReturn('');
        self::$providers_real_path = realpath(__DIR__ . '/../../../../src/Menthol/Flexible/Laravel4');
    }

    protected function tearDown()
    {
        m::close();
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
        $sp = m::mock('Menthol\Flexible\Laravel4\FlexibleServiceProvider[commands, package]', [$app]);
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

        self::$functions->shouldReceive('app_path')->once();

        $sp->shouldReceive('commands')
            ->with('menthol.flexible.commands.reindex')
            ->once()
            ->andReturn(true);

        $sp->shouldReceive('commands')
            ->with('menthol.flexible.commands.paths')
            ->once()
            ->andReturn(true);

        $sp->shouldReceive('package')
            ->with('menthol/flexible', 'flexible', self::$providers_real_path . '/../..')
            ->once();

        $app->shouldReceive('singleton')
            ->once()
            ->andReturnUsing(function ($closure) use ($app) {
                $this->assertInstanceOf('Menthol\Flexible\Config', $closure($app));
            });

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
        $sp = new FlexibleServiceProvider($app);
        $this->assertEquals([], $sp->provides());
    }

}
