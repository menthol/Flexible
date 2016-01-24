<?php namespace Menthol\Flexible\Traits;

use AspectMock\Test as am;
use Mockery as m;

class CallableTraitTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function it_should_boot_callback_trait_and_register_observer()
    {
        $husband = am::double('Husband', ['observe' => null]);

        \Husband::bootCallableTrait();

        // $husband->verifyInvoked('observe');
    }

    /**
     * @test
     * @expectedException \Exception
     */
    public function it_should_boot_callback_trait_and_throw_exception()
    {
        \Dummy::bootCallableTrait();
    }

    protected function tearDown()
    {
        m::close();
        am::clean();
    }

}
