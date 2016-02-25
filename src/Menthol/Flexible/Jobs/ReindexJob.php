<?php namespace Menthol\Flexible\Jobs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\Jobs\Job;
use Menthol\Flexible\Utilities\ElasticSearchHelper;
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
            $models = QueryHelper::findMany($modelName, $keys, true);
            foreach ($keys as $key) {
                /** @var Model|null $model */
                $model = $models->find($key);
                if ($model && $model->flexibleIsIndexable()) {
                    ElasticSearchHelper::index($model);
                } else {
                    ElasticSearchHelper::delete($modelName, $key);
                }
            }
        }

        $job->delete();
    }

}
