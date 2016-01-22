<?php namespace Menthol\Flexible\Traits;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Mockery as m;

class TransformableTraitTest extends \PHPUnit_Framework_TestCase {

    protected function tearDown()
    {
        m::close();
    }

    /**
     * @test
     */
    public function it_should_transform()
    {
        /**
         *
         * Set
         *
         */
        $husband = m::mock('Husband')->makePartial();

        /**
         *
         * Expectation
         *
         */
        $husband->shouldReceive('load->toArray')->once()->andReturn('mock');

        $config = m::mock('Menthol\Flexible\Config');

        App::shouldReceive('make')
            ->with('flexible.config')
            ->andReturn($config);

        $config->shouldReceive('get')->with('/paths\..*/')->once();
        /**
         *
         * Assertion
         *
         */
        $transformed = $husband->transform(true);

        $this->assertEquals('mock', $transformed);
    }

}
