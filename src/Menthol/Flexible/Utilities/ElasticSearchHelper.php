<?php namespace Menthol\Flexible\Utilities;

use Elasticsearch\Client;
use Illuminate\Database\Eloquent\Model;
use Menthol\Flexible\Traits\IndexableTrait;

class ElasticSearchHelper
{
    /**
     * @return Client
     */
    protected function getClient()
    {
        return App::make('Elasticsearch');
    }

    /**
     * @param Model|IndexableTrait $model
     */
    static public function index(Model $model)
    {
        static::getClient()->index([
            'index' => $model->getFlexibleIndexName(),
            'type' => $model->getFlexibleType(),
            'id' => $model->getKey(),
            'body' => TransformModel::transform($model),
        ]);
    }

    public static function delete($modelName, $key)
    {
        /** @var Model|IndexableTrait $model */
        $model = new $modelName;
        static::getClient()->delete([
            'index' => $model->getFlexibleIndexName(),
            'type' => $model->getFlexibleType(),
            'id' => $key,
        ]);
    }

    public static function prepareIndex($modelName)
    {
        /** @var Model|IndexableTrait $model */
        $model = new $modelName;


    }
}
