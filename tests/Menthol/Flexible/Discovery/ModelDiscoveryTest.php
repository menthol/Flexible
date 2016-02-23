<?php namespace Menthol\Flexible\Utilities;

/**
 * Created by PhpStorm.
 * User: nathanaellouison
 * Date: 13/02/2016
 * Time: 12:53
 */
class ModelDiscoveryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_should_discover_models()
    {
        $results = ModelDiscovery::discover(__DIR__.'/../../../Support');

        $this->assertCount(9, $results);

        $models = [
            'Menthol\Flexible\Tests\Article',
            'Menthol\Flexible\Tests\Author',
            'Menthol\Flexible\Tests\Comment',
            'Menthol\Flexible\Tests\Like',
            'Menthol\Flexible\Tests\Node',
            'Menthol\Flexible\Tests\Report',
            'Menthol\Flexible\Tests\Review',
            'Menthol\Flexible\Tests\Tag',
            'Menthol\Flexible\Tests\User',
        ];

        foreach ($models as $model) {
            $this->assertContains($model, $results);
        }
    }
}
