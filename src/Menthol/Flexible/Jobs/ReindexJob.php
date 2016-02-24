<?php namespace Menthol\Flexible\Jobs;

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

            $models = QueryHelper::findMany($modelName, $keys);

            foreach ($keys as $key) {
                $model = $models->find($key);
                if ($model && $model->flexibleIsIndexable()) {
                    // Index
                    $i = 1;
                } else {
                    // Remove from index
                    $i = 2;
                }
            }
        }

        $job->delete();
    }

}
