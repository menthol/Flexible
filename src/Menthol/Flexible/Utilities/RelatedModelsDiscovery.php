<?php namespace Menthol\Flexible\Utilities;


use Illuminate\Database\Eloquent\Model;

class RelatedModelsDiscovery
{
    static public function getRelatedModels(Model $model, $models = [])
    {
        $freshModel = QueryHelper::getFreshModel($model);
        $relations = QueryHelper::getRelationships($freshModel);

        /** @var Model $relatedModel */
        foreach (QueryHelper::flattenModel($freshModel, $relations) as $relatedModel) {
            $relatedModelName = get_class($relatedModel);
            if (in_array('Menthol\Flexible\Traits\IndexableTrait', class_uses_recursive($relatedModelName), true)) {
                $models[$relatedModelName][] = $relatedModel->getKey();
            }
        }

        foreach ($models as &$keys) {
            $keys = array_unique($keys);
        }

        return $models;
    }
}
