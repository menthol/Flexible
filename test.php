<?php

if (true) {
    ini_set('error_reporting', E_ALL);
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');

    // Composer autoloader

    require __DIR__ . '/vendor/autoload.php';

    $capsule = new \Illuminate\Database\Capsule\Manager();

    $capsule->addConnection(array(
        'driver' => 'sqlite',
        'database' => __DIR__ . '/tests/database/testing.sqlite',
        'prefix' => '',
    ));

    $capsule->bootEloquent();
}

$capsule->getConnection('default')->enableQueryLog();

/** @var \Menthol\Flexible\Tests\Article $article */
// $article = \Menthol\Flexible\Tests\Article::find(1);
$report = \Menthol\Flexible\Tests\Report::with(['articles'])->find(12);


//print_r($capsule->getConnection('default')->getQueryLog());
// print_r($report->toArray());

//return $report;

$articleModel = new \Menthol\Flexible\Tests\Article();
print_r(\Menthol\Flexible\Utilities\QueryHelper::findOne($articleModel, 30)->toArray());

// print_r(\Menthol\Flexible\Utilities\RelatedModelsDiscovery::getRelatedModels($report));


//$article->load('author');
//print_r($article->toArray());
//print_r(\Menthol\Flexible\Discovery\ModelDiscovery::discover(__DIR__ . '/tests'));

//print_r(\Menthol\Flexible\Tests\Article::getConnectionResolver()->connection());
// $models = \Menthol\Flexible\Utils::findModels(__DIR__ . '/tests');

// print_r(\Menthol\Flexible\Utils::findRelations($models, 5));


