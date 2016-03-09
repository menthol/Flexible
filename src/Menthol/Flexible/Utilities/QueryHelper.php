<?php namespace Menthol\Flexible\Utilities;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Psr\Log\InvalidArgumentException;

class QueryHelper
{
    static public function newQueryWithoutScopes($model, $index = false, $relationships = true)
    {
        $model = self::getModel($model);

        $conn = $model->getConnection();

        $grammar = $conn->getQueryGrammar();

        $processor = $conn->getPostProcessor();

        $baseQueryBuilder = new Builder($conn, $grammar, $processor);

        $builder = $model->newEloquentBuilder($baseQueryBuilder);

        if ($relationships) {
            $builder->with(self::getRelationships($model, $index));
        }

        return $builder->setModel($model);
    }

    static public function flattenModel(Model $model, array $relations)
    {
        $models[] = $model;

        foreach ($relations as $relation) {
            $remainingRelation = null;
            if (strpos($relation, '.') !== false) {
                list($relation, $remainingRelation) = explode('.', $relation, 2);
            }

            /** @var Collection|Model[] $collection */
            $collection = $model->$relation;
            if (empty($collection)) {
                continue;
            }

            if ($collection instanceof Model) {
                $collection = new Collection([$collection]);
            }

            if (!$collection instanceof Collection) {
                throw new InvalidArgumentException();
            }

            foreach ($collection as $relationModel) {
                $models[] = $relationModel;

                if ($remainingRelation) {
                    foreach (static::flattenModel($relationModel, [$remainingRelation]) as $childModel) {
                        $models[] = $childModel;
                    }
                }
            }
        }

        return $models;
    }

    static public function getRelationships($model, $index = false)
    {
        if ($index) {
            if (method_exists($model, 'getFlexibleIndexRelationships')) {
                return $model->getFlexibleIndexRelationships();
            }

            return [];
        }

        if (method_exists($model, 'getFlexibleObservedRelationships')) {
            return $model->getFlexibleObservedRelationships();
        }
        return [];
    }

    static public function getFreshModel(Model $model, $index = false)
    {
        $query = static::newQueryWithoutScopes($model, $index);
        return $query->where($model->getKeyName(), $model->getKey())->first();
    }

    static public function findOne($model, $id, $index = false)
    {
        return static::findMany($model, [$id], $index)->first();
    }

    static public function findMany($model, $ids, $index = false)
    {
        $query = static::newQueryWithoutScopes($model, $index);
        return $query->whereIn(static::getModel($model)->getKeyName(), $ids)->get();
    }

    /**
     * @param $model
     * @return Model
     */
    protected static function getModel($model)
    {
        if (is_string($model) && class_exists($model) && is_subclass_of($model, 'Illuminate\Database\Eloquent\Model')) {
            return new $model;
        }

        return $model;
    }
}
