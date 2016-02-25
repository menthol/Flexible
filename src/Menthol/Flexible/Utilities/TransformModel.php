<?php namespace Menthol\Flexible\Utilities;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class TransformModel
{
    static public function transform(Model $model, Model $rootModel = null, $parentRelation = '')
    {
        if (is_null($rootModel)) {
            $rootModel = $model;
        }
        $data = [];

        foreach ($model->getRelations() as $relation => $collection) {
            $relationName = $parentRelation ? $parentRelation . '.' . $relation : $relation;
            if ($collection instanceof Model) {
                $data[$relation] = static::transform($collection, $rootModel, $relationName);
            } elseif ($collection instanceof Collection) {
                foreach ($collection as $relatedModel) {
                    if (!$relatedModel instanceof Model) {
                        throw new \LogicException;
                    }
                    $data[$relation][] = static::transform($relatedModel, $rootModel, $relationName);
                }
            } else {
                throw new \LogicException;
            }
        }

        $appendKeys = $rootModel->getFlexibleAppendKeys(get_class($model), $parentRelation);
        foreach ($appendKeys as $key) {
            $data[$key] = $model->getAttributeValue($key);
        }

        foreach ($model->getOriginal() as $field => $value) {
            $data[$field] = $value;
        }

        $hiddenKeys = $rootModel->getFlexibleHiddenKeys(get_class($model), $parentRelation);
        foreach ($hiddenKeys as $key) {
            unset($data[$key]);
        }

        $data['_model'] = get_class($model);
        $data['_key'] = $model->getKey();

        return $data;
    }
}
