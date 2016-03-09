<?php namespace Menthol\Flexible\Jobs;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\Jobs\Job;
use Menthol\Flexible\Traits\IndexableTrait;
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
            /** @var Collection $models */
            $models = QueryHelper::findMany($modelName, $keys, true);
            foreach ($keys as $key) {
                /** @var Model|IndexableTrait|null $model */
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
