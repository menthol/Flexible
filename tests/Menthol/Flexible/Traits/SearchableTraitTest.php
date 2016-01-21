<?php namespace Menthol\Flexible\Traits;

use Husband;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Facade;
use Menthol\Flexible\Proxy;
use Mockery as m;
use AspectMock\Test as am;

class SearchableTraitTest extends \PHPUnit_Framework_TestCase {

    protected function tearDown()
    {
        m::close();
        am::clean();
    }

    /**
     * @test
     */
    public function it_should_get_a_proxy()
    {
        /**
         *
         * Set
         *
         */
        $proxy = m::mock('Menthol\Flexible\proxy');

        /**
         *
         * Expectation
         *
         */
        Facade::clearResolvedInstances();
        \Husband::clearProxy();

        App::shouldReceive('make')
            ->with('menthol.flexible.proxy', m::type('Illuminate\Database\Eloquent\Model'))
            ->andReturn($proxy);

        /**
         *
         *
         * Assertion
         *
         */
        $proxy1 = \Husband::getProxy();

        $this->assertSame($proxy, $proxy1);

        $proxy2 = \Husband::getProxy();

        $this->assertSame($proxy, $proxy2);
    }

    /**
     * @test
     * @expectedException \Exception
     */
    public function it_should_throw_an_exception_if_included_in_a_non_eloquent_model()
    {
        /**
         *
         * Set
         *
         */
        \Dummy::getProxy();
    }

    /**
     * @test
     */
    public function it_should_call_methods_on_the_proxy()
    {
        /**
         *
         * Set
         *
         */
        $proxy = m::mock('Menthol\Flexible\proxy');
        $husband = am::double('Husband', ['getProxy' => $proxy]);

        /**
         *
         * Expectation
         *
         */
        $proxy->shouldReceive('search')
            ->with('*')
            ->once()
            ->andReturn('result_static');

        $proxy->shouldReceive('search')
            ->with('**')
            ->once()
            ->andReturn('result');

        /**
         *
         * Assertion
         *
         */
        $result = \Husband::search('*');

        $this->assertEquals('result_static', $result);

        $result = \Husband::search('**');

        $this->assertEquals('result', $result);

        $husband->verifyInvoked('getProxy');
    }

    /**
     * @test
     * @expectedException \BadMethodCallException
     */
    public function it_should_not_call_methods_on_the_proxy()
    {
        /**
         *
         * Expectation
         *
         */
        App::clearResolvedInstance('app');
        App::shouldReceive('make')->with('Elasticsearch')->andReturn(true);
        App::shouldReceive('make')->with('menthol.flexible.index', m::type('array'))->andReturn(true);

        /**
         *
         * Assertion
         *
         */
        // Overrule the proxy defined in previous tests
        am::double('Husband', ['getProxy' => new Proxy(new Husband)]);

        // Call a non existing method
        \Husband::bogus('*');
    }

    /**
     * @test
     */
    public function it_should_get_elasticsearch_id()
    {
        /**
         * Assertions
         */
        $husband = new Husband();

        $this->assertEquals('dummy_id', $husband->getEsId());
    }

}
