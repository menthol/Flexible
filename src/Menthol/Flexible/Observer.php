<?php namespace Menthol\Flexible;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Queue;
use Menthol\Flexible\Utilities\RelatedModelsDiscovery;
use Menthol\Flexible\Jobs\ReindexJob;

class Observer
{
    public function created(Model $model)
    {
        // Update all related model documents to reflect that $model has been removed
        Queue::push(ReindexJob::class, RelatedModelsDiscovery::getRelatedModels($model));
    }

    public function updating(Model $model)
    {
        // Update all related model documents to reflect that $model has been removed
        Queue::push(ReindexJob::class, RelatedModelsDiscovery::getRelatedModels($model));
    }

    public function updated(Model $model)
    {
        // Update all related model documents to reflect that $model has been removed
        Queue::push(ReindexJob::class, RelatedModelsDiscovery::getRelatedModels($model));
    }

    public function deleting(Model $model)
    {
        // Update all related model documents to reflect that $model has been removed
        Queue::push(ReindexJob::class, RelatedModelsDiscovery::getRelatedModels($model));
    }

    public function restored(Model $model)
    {
        // Update all related model documents to reflect that $model has been removed
        Queue::push(ReindexJob::class, RelatedModelsDiscovery::getRelatedModels($model));
    }

}
