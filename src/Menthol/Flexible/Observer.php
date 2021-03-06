<?php namespace Menthol\Flexible;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Queue;

class Observer
{

    /**
     * Model delete event handler
     *
     * @param Model $model
     */
    public function deleted(Model $model)
    {
        // Delete corresponding $model document from Elasticsearch
        Queue::push('Menthol\Flexible\Jobs\DeleteJob', [get_class($model) . ':' . $model->getKey()]);

        // Update all related model documents to reflect that $model has been removed
        Queue::push('Menthol\Flexible\Jobs\ReindexJob', $this->findAffectedModels($model, true));
    }

    /**
     * Model save event handler
     *
     * @param Model $model
     */
    public function saved(Model $model)
    {
        if ($model::$__es_enable && $model->shouldIndex()) {
            Queue::push('Menthol\Flexible\Jobs\ReindexJob', $this->findAffectedModels($model));
        }
    }

    /**
     * Find all searchable models that are affected by the model change
     *
     * @param Model $model
     * @return array
     */
    private function findAffectedModels(Model $model, $excludeCurrent = false)
    {
        // Temporary array to store affected models
        $affectedModels = [];

        $paths = App::make('Menthol\Flexible\Config')->get('reversedPaths.' . get_class($model), []);

        foreach ((array)$paths as $path) {
            if (!empty($path)) {
                $model = $model->load($path);

                // Explode the path into an array
                $path = explode('.', $path);

                // Define a little recursive function to walk the relations of the model based on the path
                // Eventually it will queue all affected searchable models for reindexing
                $walk = function ($relation, array $path) use (&$walk, &$affectedModels) {
                    $segment = array_shift($path);

                    $relation = $relation instanceof Collection ? $relation : new Collection([$relation]);

                    foreach ($relation as $record) {
                        if ($record instanceof Model) {
                            if (!empty($segment)) {
                                if (array_key_exists($segment, $record->getRelations())) {
                                    $walk($record->getRelation($segment), $path);
                                } else {
                                    // Apparently the relation doesn't exist on this model, so skip the rest of the path as well
                                    return;
                                }
                            } else {
                                if (in_array('Menthol\Flexible\Traits\SearchableTrait', class_uses($record))) {
                                    $affectedModels[] = get_class($record) . ':' . $record->getKey();
                                }
                            }
                        }
                    }
                };

                $walk($model->getRelation(array_shift($path)), $path);
            } else if (!$excludeCurrent) {
                if (in_array('Menthol\Flexible\Traits\SearchableTrait', class_uses($model))) {
                    $affectedModels[] = get_class($model) . ':' . $model->getKey();
                }
            }
        }

        return array_unique($affectedModels);
    }

}
