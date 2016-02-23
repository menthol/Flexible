<?php namespace Menthol\Flexible\Jobs;

use Exception;
use Illuminate\Queue\Jobs\Job;
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

            $model = new $modelName;

            $models = QueryHelper::findMany($model, $keys);

            foreach ($models as $model) {
                $model->flexibleRefreshDoc();
            }

            // refrechDoc
        }

        $job->delete();
    }

}
