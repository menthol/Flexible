<?php namespace Menthol\Flexible\Utilities;


use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Menthol\Flexible\Traits\IndexableTrait;

class TransformModel
{
    static public function transform(Model $model, Model $rootModel = null, $parentRelation = '')
    {
        /** @var Model|IndexableTrait $rootModel */
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
            } elseif (is_null($collection)) {
                $data[$relation] = null;
            } else {
                throw new \LogicException;
            }
        }

        $appendKeys = $rootModel->getFlexibleAppendKeys(get_class($model), $parentRelation);
        foreach ($appendKeys as $key) {
            if ($model->hasGetMutator($key)) {
                $data[$key] = $model->getAttribute($key);
            }
        }

        $fields = array_keys($model->getOriginal());
        if (count($rootModel->getFlexibleOnlyKeys(get_class($model), $parentRelation)) > 0) {
            $fields = $rootModel->getFlexibleOnlyKeys(get_class($model), $parentRelation);
        }

        foreach ($fields as $field) {
            $data[$field] = $model->getOriginal($field);
        }

        $hiddenKeys = $rootModel->getFlexibleHiddenKeys(get_class($model), $parentRelation);
        foreach ($hiddenKeys as $key) {
            unset($data[$key]);
        }

        $data['_model'] = get_class($model);
        $data[$model->getKeyName()] = $model->getKey();

        return $data;
    }

    static public function hydrate($attributes)
    {
        $modelName = $attributes['_model'];
        unset($attributes['_model']);

        /** @var Model|IndexableTrait $model */
        $model = new $modelName;

        foreach ($attributes as $key => $attribute) {
            if ( ! is_array($attribute)) {
                continue;
            }

            unset($attributes[$key]);

            if (isset($attribute['_model'])) {
                $relatedModel = static::hydrate($attribute);
                $model->setRelation($key, $relatedModel);
            }
            else {
                $collection = new EloquentCollection();

                foreach ($attribute as $modelAttribute) {
                    $collection->push(static::hydrate($modelAttribute));
                }

                $model->setRelation($key, $collection);
            }
        }

        $model->setRawAttributes($attributes, true);
        $model->exists = true;

        return $model;
    }
}
