<?php namespace Menthol\Flexible;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Queue;
use Menthol\Flexible\Utilities\RelatedModelsDiscovery;

class Observer
{
    protected function observeRelatedModels(Model $model)
    {
        $model->flexibleRelatedModels = RelatedModelsDiscovery::getRelatedModels($model, $model->flexibleRelatedModels ?: []);
    }

    protected function pushReindexJob(Model $model)
    {
        $relatedModels = RelatedModelsDiscovery::getRelatedModels($model, $model->flexibleRelatedModels ?: []);

        if (count($relatedModels, COUNT_RECURSIVE) > 750) {
            foreach ($relatedModels as $model => $keys) {
                foreach (array_chunk($keys, 750) as $chunkedKeys) {
                    Queue::push('Menthol\Flexible\Jobs\ReindexJob', [$model => $chunkedKeys]);
                }
            }
        } else {
            Queue::push('Menthol\Flexible\Jobs\ReindexJob', $relatedModels);
        }
    }

    public function created(Model $model)
    {
        $this->pushReindexJob($model);
    }

    public function updating(Model $model)
    {
        $this->observeRelatedModels($model);
    }

    public function updated(Model $model)
    {
        $this->pushReindexJob($model);
    }

    public function deleting(Model $model)
    {
        $this->observeRelatedModels($model);
    }

    public function deleted(Model $model)
    {
        $this->pushReindexJob($model);
    }

    public function restoring(Model $model)
    {
        $this->observeRelatedModels($model);
    }

    public function restored(Model $model)
    {
        $this->pushReindexJob($model);
    }

}
