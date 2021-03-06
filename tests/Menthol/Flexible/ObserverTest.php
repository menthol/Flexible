<?php namespace Menthol\Flexible;

use AspectMock\Test as am;
use Husband;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Queue;
use Mockery as m;

class ObserverTest extends \PHPUnit_Framework_TestCase
{

    public static $functions;

    protected function setUp()
    {
        self::$functions = m::mock();
    }


    protected function tearDown()
    {
        m::close();
        am::clean();
    }

    /**
     * @test
     */
    public function it_should_not_reindex_on_model_save()
    {
        /**
         *
         * Expectation
         *
         */
        $queue = am::double('Illuminate\Support\Facades\Queue', ['push' => null]);

        $husband = m::mock('Husband');
        $husband->shouldReceive('shouldIndex')->andReturn(false);

        /**
         *
         * Assertion
         *
         */
        with(new Observer)->saved($husband);

        $queue->verifyNeverInvoked('push');
    }

    /**
     * @test
     */
    public function it_should_reindex_on_model_save()
    {
        /**
         *
         * Expectation
         *
         */
        Facade::clearResolvedInstances();

        $config = m::mock('Menthol\Flexible\Config');
        $proxy = m::mock('Menthol\Flexible\Proxy');
        $proxy->shouldReceive('shouldIndex')->andReturn(true);

        App::shouldReceive('make')
            ->with('menthol.flexible.proxy', m::type('Illuminate\Database\Eloquent\Model'))
            ->andReturn($proxy);

        App::shouldReceive('make')
            ->with('Menthol\Flexible\Config')
            ->andReturn($config);

        $config->shouldReceive('get')
            ->with('reversedPaths.Husband', [])
            ->once()
            ->andReturn(['', 'wife', 'children', 'children.toys']);

        Queue::shouldReceive('push')
            ->with('Menthol\Flexible\Jobs\ReindexJob', [
                'Husband:2',
                'Wife:2',
                'Child:2',
                'Toy:2'
            ])->once();

        /**
         *
         * Assertion
         *
         */
        $husband = \Husband::find(2);
        $husband->clearProxy();

        with(new Observer)->saved($husband);

        /**
         *
         * Expectation
         *
         */
        Facade::clearResolvedInstances();

        $config = m::mock('Menthol\Flexible\Config');
        $proxy = m::mock('Menthol\Flexible\Proxy');
        $proxy->shouldReceive('shouldIndex')->andReturn(true);

        App::shouldReceive('make')
            ->with('menthol.flexible.proxy', m::type('Illuminate\Database\Eloquent\Model'))
            ->andReturn($proxy);

        App::shouldReceive('make')
            ->with('Menthol\Flexible\Config')
            ->andReturn($config);

        $config->shouldReceive('get')
            ->with('reversedPaths.Toy', [])
            ->once()
            ->andReturn(['', 'children', 'children.mother.husband', 'children.mother']);

        Queue::shouldReceive('push')
            ->with('Menthol\Flexible\Jobs\ReindexJob', [
                'Toy:2',
                'Child:8',
                'Child:2',
                'Husband:8',
                'Husband:2',
                'Wife:8',
                'Wife:2'
            ])->once();

        /**
         *
         * Assertion
         *
         */
        $toy = \Toy::find(2);

        with(new Observer)->saved($toy);
    }

    /**
     * @test
     */
    public function it_should_reindex_on_model_delete()
    {
        /**
         *
         * Expectation
         *
         */
        Facade::clearResolvedInstances();

        Queue::shouldReceive('push')
            ->with('Menthol\Flexible\Jobs\DeleteJob', ['Husband:2'])
            ->once();

        $config = m::mock('Menthol\Flexible\Config');

        App::shouldReceive('make')
            ->with('Menthol\Flexible\Config')
            ->andReturn($config);

        $config->shouldReceive('get')
            ->with('/^reversedPaths\..*$/', [])
            ->once()
            ->andReturn(['', 'wife', 'children', 'children.toys']);

        Queue::shouldReceive('push')
            ->with('Menthol\Flexible\Jobs\ReindexJob', ['Wife:2', 'Child:2', 'Toy:2'])
            ->once();

        $husband = \Husband::find(2);

        with(new Observer)->deleted($husband);
    }

}
