<?php namespace Menthol\Flexible;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Queue;
use Menthol\Flexible\Utilities\RelatedModelsDiscovery;
use Menthol\Flexible\Jobs\ReindexJob;

class Observer
{
    public function created(Model $model)
    {
        Queue::push(ReindexJob::class, RelatedModelsDiscovery::getRelatedModels($model));
    }

    public function updating(Model $model)
    {
        $model->flexibleRelatedModels = RelatedModelsDiscovery::getRelatedModels($model);
    }

    public function updated(Model $model)
    {
        Queue::push(ReindexJob::class, RelatedModelsDiscovery::getRelatedModels($model, $model->flexibleRelatedModels));
        $model->flexibleRelatedModels = [];
    }

    public function deleting(Model $model)
    {
        $model->flexibleRelatedModels = RelatedModelsDiscovery::getRelatedModels($model);
    }

    public function deleted(Model $model)
    {
        Queue::push(ReindexJob::class, RelatedModelsDiscovery::getRelatedModels($model, $model->flexibleRelatedModels));
        $model->flexibleRelatedModels = [];
    }

    public function restoring()
    {
        $model->flexibleRelatedModels = RelatedModelsDiscovery::getRelatedModels($model);
    }

    public function restored(Model $model)
    {
        Queue::push(ReindexJob::class, RelatedModelsDiscovery::getRelatedModels($model, $model->flexibleRelatedModels));
        $model->flexibleRelatedModels = [];
    }

}
