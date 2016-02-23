<?php namespace Menthol\Flexible\Utilities;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Psr\Log\InvalidArgumentException;

class QueryHelper
{
    static public function newQueryWithoutScopes(Model $model)
    {
        $conn = $model->getConnection();

        $grammar = $conn->getQueryGrammar();

        $processor = $conn->getPostProcessor();

        $baseQueryBuilder = new Builder($conn, $grammar, $processor);

        $builder = $model->newEloquentBuilder($baseQueryBuilder);

        $builder->with(self::getRelationships(get_class($model)));

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

    static public function getRelationships($modelName)
    {
        if (method_exists($modelName, 'getFlexibleRelationships')) {
            return $modelName::getFlexibleRelationships();
        }
        return [];
    }

    static public function getFreshModel(Model $model)
    {
        $query = static::newQueryWithoutScopes($model);
        return $query->where($model->getKeyName(), $model->getKey())->first();
    }

    static public function findOne($model, $id)
    {
        return static::findMany($model, [$id])->first();
    }

    static public function findMany(Model $model, $ids)
    {
        $query = static::newQueryWithoutScopes($model);
        return $query->whereIn($model->getKeyName(), $ids)->get();
    }
}
