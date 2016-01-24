<?php namespace Menthol\Flexible\Jobs;

use AspectMock\Test as am;
use Mockery as m;

class DeleteJobTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function it_should_fire_job()
    {
        /**
         *
         * Set
         *
         */
        $app = m::mock('Illuminate\Foundation\Application');
        $config = m::mock('Menthol\Flexible\Config');
        $logger = m::mock('Monolog\Logger');
        am::double('Husband', ['deleteDoc' => true]);

        $job = m::mock('Illuminate\Queue\Jobs\Job');
        $models = [
            'Husband:999'
        ];

        /**
         *
         * Expectation
         *
         */
        $logger->shouldReceive('info')->with('Deleting Husband with ID: 999 from Elasticsearch');
        $config->shouldReceive('get')->with('logger', 'menthol.flexible.logger')->andReturn('menthol.flexible.logger');
        $app->shouldReceive('make')->with('menthol.flexible.logger')->andReturn($logger);
        $job->shouldReceive('delete')->once();

        /**
         *
         * Assertion
         *
         */
        with(new DeleteJob($app, $config))->fire($job, $models);
    }

    protected function tearDown()
    {
        m::close();
        am::clean();
    }

}
