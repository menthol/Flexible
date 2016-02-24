<?php namespace Menthol\Flexible\Jobs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\Jobs\Job;
use Illuminate\Support\Facades\App;
use Menthol\Flexible\Utilities\QueryHelper;

/**
 * Class ReindexJob
 *
 * @package Menthol\Flexible\Jobs
 */
class ReindexJob
{
    public function fire(Job $job, $modelDefinitions)
    {
        foreach ($modelDefinitions as $modelName => $keys) {

            $models = QueryHelper::findMany($modelName, $keys);
            $client = App::make('Elasticsearch');
            $index_prefix = App::make('Menthol\Flexible\Config')->get('elasticsearch.index_prefix', '');

            foreach ($keys as $key) {
                /** @var Model|null $model */
                $model = $models->find($key);
                if ($model && $model->flexibleIsIndexable()) {
                    $client->index([
                        'index' => $index_prefix.$model->getTable(),
                        'id' => $key,
                        'type' => str_singular($model->getTable()),
                        'body' => [],
                    ]);
                } else {
                    $model = new $modelName;
                    $client->delete([
                        'index' => $index_prefix.$model->getTable(),
                        'id' => $key,
                        'type' => str_singular($model->getTable()),
                    ]);
                }
            }
        }

        $job->delete();
    }

}
