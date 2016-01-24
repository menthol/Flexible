<?php namespace Menthol\Flexible\Commands;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Mockery as m;
use Symfony\Component\Console\Input\InputOption;

/**
 * Global function mocks
 */
function app_path()
{
    return PathsCommandTest::$functions->app_path();
}

function constant($const)
{
    return PathsCommandTest::$functions->constant($const);
}

function base_path()
{
    return PathsCommandTest::$functions->base_path();
}

function config_path()
{
    return PathsCommandTest::$functions->config_path();
}

/**
 * Class PathsCommandTest
 * @package Menthol\Flexible\Commands
 * @preserveGlobalState disabled
 */
class PathsCommandTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Mockery\Mock $functions
     */
    public static $functions;

    /**
     *
     */
    public function setUp()
    {
        parent::setUp();

        self::$functions = m::mock();
    }

    /**
     *
     */
    protected function tearDown()
    {
        m::close();
    }

    /**
     * @test
     */
    public function it_should_get_options()
    {
        /**
         *
         * Set
         *
         **/
        $command = m::mock('Menthol\Flexible\Commands\PathsCommand');
        $options = array(
            array('dir', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'Directory to scan for searchable models', null, ''),
            array('relations', null, InputOption::VALUE_NONE, 'Include related Eloquent models', null),
            array('write-config', null, InputOption::VALUE_NONE, 'Include the compiled paths in the package configuration', null),
        );

        /**
         *
         * Assertion
         *
         **/
        $this->assertEquals($options, $command->getOptions());
    }

    /**
     * @test
     */
    public function it_should_get_arguments()
    {
        /**
         *
         * Set
         *
         **/
        $command = m::mock('Menthol\Flexible\Commands\PathsCommand');
        $arguments = array(
            array('model', InputOption::VALUE_OPTIONAL, 'Eloquent model to find paths for', null)
        );

        /**
         *
         * Assertion
         *
         **/
        $this->assertEquals($arguments, $command->getArguments());
    }

    /**
     * @test
     */
    public function it_should_fire_with_models_and_config()
    {
        /**
         *
         * Set
         *
         * @var \Mockery\Mock $command
         */
        $command = m::mock('Menthol\Flexible\Commands\PathsCommand')->makePartial();
        $command->shouldAllowMockingProtectedMethods();

        File::clearResolvedInstance('files');
        File::shouldReceive('put')->once()->andReturn(true);
        File::shouldReceive('exists')->once()->andReturn(true);

        App::shouldReceive('make')->andReturn(true);

        /**
         *
         * Expectation
         *
         */
        $command->shouldReceive('argument')
            ->with('model')
            ->once()
            ->andReturn(['Husband']);

        $command->shouldReceive('option')
            ->with('dir')
            ->once()
            ->andReturn([__DIR__ . '/../../../Support/Stubs']);

        $command->shouldReceive('option')
            ->with('write-config')
            ->once()
            ->andReturn(true);

        $command->shouldReceive('option')
            ->with('relations')
            ->times(17)
            ->andReturn(true);

        $command->shouldReceive('error', 'confirm', 'call', 'info')->andReturn(true);

        /**
         *
         * Assertion
         *
         */
        $command->fire();


        $expected = [
            'Husband' => ['wife.children.toys'],
            'Child' => ['mother', 'father', 'toys'],
            'Toy' => ['children.mother', 'children.father'],
            'Wife' => ['husband', 'children.toys'],
            'House\\Item' => []
        ];
        $actual = $command->getPaths();

        foreach ($expected as $model => $paths) {
            $this->assertArrayHasKey($model, $actual);

            foreach ($paths as $path) {
                $this->assertContains($path, $actual[$model]);
            }
        }

        $expected = [
            'Husband' => ['', 'wife', 'children', 'children.toys'],
            'Child' => ['mother.husband', 'mother', '', 'toys'],
            'Toy' => ['children.mother.husband', 'children.mother', 'children', ''],
            'Wife' => ['husband', '', 'children', 'children.toys'],
            'House\\Item' => ['']
        ];
        $actual = $command->getReversedPaths();

        foreach ($expected as $model => $paths) {
            $this->assertArrayHasKey($model, $actual);

            foreach ($paths as $path) {
                $this->assertContains($path, $actual[$model]);
            }
        }
    }

    /**
     * @test
     */
    public function it_should_fire_without_models()
    {
        /**
         * Set
         *
         * @var \Mockery\Mock $command
         */
        $command = m::mock('Menthol\Flexible\Commands\PathsCommand')->makePartial();
        $command->shouldAllowMockingProtectedMethods();

        App::shouldReceive('make')->andReturn(true);

        /**
         *
         * Expectation
         *
         */
        $command->shouldReceive('argument')
            ->with('model')
            ->once()
            ->andReturn([]);


        $command->shouldReceive('option')
            ->with('dir')
            ->once()
            ->andReturn([]);


        $command->shouldReceive('compilePaths', 'error', 'confirm', 'call', 'info')->andReturn(true);

        /**
         *
         * Assertion
         *
         */
        $command->fire();

        $this->assertEquals(
            [],
            $command->getPaths()
        );

        $this->assertEquals(
            [],
            $command->getReversedPaths()
        );
    }

    /**
     * @test
     */
    public function it_should_fire_without_config()
    {
        /**
         * Set
         *
         * @var \Mockery\Mock $command
         */
        $command = m::mock('Menthol\Flexible\Commands\PathsCommand')->makePartial();
        $command->shouldAllowMockingProtectedMethods();

        App::shouldReceive('make')->andReturn(true);

        /**
         *
         * Expectation
         *
         */
        $command->shouldReceive('argument')
            ->with('model')
            ->once()
            ->andReturn(['Husband']);

        $command->shouldReceive('option')
            ->with('dir')
            ->once()
            ->andReturn([__DIR__ . '/../../../Support/Stubs']);

        $command->shouldReceive('option')
            ->with('write-config')
            ->once()
            ->andReturn(false);

        $command->shouldReceive('compilePaths', 'error', 'confirm', 'call', 'info')->andReturn(true);

        /**
         *
         * Assertion
         *
         */
        $command->fire();
    }

    /**
     * @test
     */
    public function it_should_fire_with_laravel_and_config_confirmed()
    {
        /**
         *
         * Set
         *
         * @var \Mockery\Mock $command
         */
        $command = m::mock('Menthol\Flexible\Commands\PathsCommand')->makePartial();
        $command->shouldAllowMockingProtectedMethods();

        File::clearResolvedInstance('files');
        File::shouldReceive('put')->once()->andReturn(true);
        File::shouldReceive('exists')->once()->andReturn(false);

        App::shouldReceive('make')->andReturn(true);

        /**
         *
         * Expectation
         *
         */
        $command->shouldReceive('getLaravel')
            ->once()
            ->andReturn(true);

        $command->shouldReceive('argument')
            ->with('model')
            ->once()
            ->andReturn(['Husband']);

        $command->shouldReceive('option')
            ->with('dir')
            ->once()
            ->andReturn([__DIR__ . '/../../../Support/Stubs']);

        $command->shouldReceive('option')
            ->with('write-config')
            ->once()
            ->andReturn(true);

        $command->shouldReceive('confirm')->once()->andReturn(true);

        $command->shouldReceive('compilePaths', 'error', 'call', 'info')
            ->andReturn(true);

        /**
         *
         * Assertion
         *
         */
        $command->fire();
    }

    /**
     * @test
     */
    public function it_should_fire_with_config_not_confirmed()
    {
        /**
         * Set
         *
         * @var \Mockery\Mock $command
         */
        $command = m::mock('Menthol\Flexible\Commands\PathsCommand')->makePartial();
        $command->shouldAllowMockingProtectedMethods();

        File::clearResolvedInstance('files');
        File::shouldReceive('exists')->once()->andReturn(false);

        App::shouldReceive('make')->andReturn(true);

        /**
         * Expectation
         */
        $command->shouldReceive('getLaravel')
            ->once()
            ->andReturn(true);

        $command->shouldReceive('argument')
            ->with('model')
            ->once()
            ->andReturn(['Husband']);

        $command->shouldReceive('option')
            ->with('dir')
            ->once()
            ->andReturn([__DIR__ . '/../../../Support/Stubs']);

        $command->shouldReceive('option')
            ->with('write-config')
            ->once()
            ->andReturn(true);

        $command->shouldReceive('confirm')->once()->andReturn(false);

        $command->shouldReceive('compilePaths', 'error', 'call', 'info')
            ->andReturn(true);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */
        $command->fire();
    }

}
