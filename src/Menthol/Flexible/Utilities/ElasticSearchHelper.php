<?php namespace Menthol\Flexible\Utilities;

use Elasticsearch\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Menthol\Flexible\Traits\IndexableTrait;

class ElasticSearchHelper
{
    /**
     * @return Client
     */
    static protected function getClient()
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
            'type'  => $model->getFlexibleType(),
            'id'    => $model->getKey(),
            'body'  => TransformModel::transform($model),
        ]);
    }

    static public function delete($modelName, $key)
    {
        /** @var Model|IndexableTrait $model */
        $model = new $modelName;
        static::getClient()->delete([
            'index' => $model->getFlexibleIndexName(),
            'type'  => $model->getFlexibleType(),
            'id'    => $key,
        ]);
    }

    static public function prepareIndex($modelName)
    {
        /** @var Model|IndexableTrait $model */
        $model = new $modelName;

        $indices = static::getClient()->indices();

        if (in_array($model->getFlexibleIndexName(), array_keys($indices->getAliases()))) {
            $indices->delete([
                'index' => $model->getFlexibleIndexName(),
            ]);
        }

        $tmpAliasName = $model->getFlexibleIndexName().'_tmp';

        if ($indices->existsAlias(['name' => $tmpAliasName])) {
            $indices->delete([
                'index' => $tmpAliasName,
            ]);
        }

        $uniqueIndexName = $model->getFlexibleIndexName().'_'.date('YmdHis');
        $param = [
            'index' => $uniqueIndexName,
            'body' => [
                'settings' => $model->getFlexibleIndexSettings(),
            ],
        ];

        $indices->create($param);
        $indices->putAlias([
            'index' => $uniqueIndexName,
            'name'  => $tmpAliasName,
        ]);
    }
}
