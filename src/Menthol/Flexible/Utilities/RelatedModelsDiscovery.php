<?php namespace Menthol\Flexible\Utilities;


use Illuminate\Database\Eloquent\Model;
use Menthol\Flexible\Traits\IndexableTrait;

class RelatedModelsDiscovery
{
    static public function getRelatedModels(Model $model, $models = [])
    {
        $freshModel = QueryHelper::getFreshModel($model);
        $relations = QueryHelper::getRelationships(get_class($freshModel));

        /** @var Model $relatedModel */
        foreach (QueryHelper::flattenModel($freshModel, $relations) as $relatedModel) {
            $relatedModelName = get_class($relatedModel);
            if (in_array(IndexableTrait::class, class_uses_recursive($relatedModelName), true)) {
                $models[$relatedModelName][] = $relatedModel->getKey();
            }
        }

        foreach ($models as &$keys) {
            $keys = array_unique($keys);
        }

        return $models;
    }
}
