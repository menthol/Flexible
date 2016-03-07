<?php namespace Menthol\Flexible\Traits;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Menthol\Flexible\FlexibleCollection;
use Menthol\Flexible\Utilities\ElasticSearchHelper;
use Menthol\Flexible\Utilities\TransformModel;

trait IndexableTrait
{
    use ObservableTrait;

    public $flexibleRawResults = null;

    static public function search($term)
    {
        $model = new static;
        $result = ElasticSearchHelper::search([
            'index' => $model->getFlexibleIndexName(),
            'type' => $model->getFlexibleType(),
            'body' => $term,
        ]);
        $collection = new FlexibleCollection();
        $collection->setFlexibleRawResults($result);

        if (isset($result['hits']['hits'])) {
            foreach ($result['hits']['hits'] as $hit) {
                $transformedModel = TransformModel::hydrate($hit['_source']);
                $transformedModel->setFlexibleRawResults($hit);
                $collection->add($transformedModel);
            }
        }

        return $collection;
    }

    static public function searchById($id)
    {
        $model = new static;
        $result = ElasticSearchHelper::get([
            'index' => $model->getFlexibleIndexName(),
            'type' => $model->getFlexibleType(),
            'id' => $id,
        ]);

        if ( ! empty($result['found'])) {
            /** @var Model|IndexableTrait $model */
            $model =  TransformModel::hydrate($result['_source']);

            $model->setFlexibleRawResults($result);
            return $model;
        }

        return false;
    }

    public function setFlexibleRawResults($flexibleRawResults)
    {
        $this->flexibleRawResults = $flexibleRawResults;
    }

    public function getFlexibleRawResults()
    {
        return $this->flexibleRawResults;
    }

    public function getFlexibleRawValue($key, $default = null)
    {
        return array_get($this->flexibleRawResults['_source'], $key, $default);
    }

    public function getFlexibleScore()
    {
        return array_get($this->flexibleRawResults, '_score');
    }

    static public function bootIndexableTrait()
    {
        if (!new static instanceof Model) {
            throw new Exception('This trait can ony be used in Eloquent models.');
        }
    }

    public function getFlexibleIndexRelationships()
    {
        if (property_exists($this, 'flexibleIndexRelationships')) {
            return $this->flexibleIndexRelationships;
        }

        return $this->getFlexibleRelationships();
    }

    public function getFlexibleAppendKeys($modelName, $relation = null)
    {
        if (!property_exists(get_called_class(), 'flexibleAppends')) {
            return [];
        }

        $appends = $this->flexibleAppends;

        if (!empty($relation)) {
            if (!array_key_exists($relation, $appends)) {
                return [];
            }

            $appends = $appends[$relation];
        }

        $keys = [];

        foreach ($appends as $append) {
            if (!is_array($append)) {
                $keys[] = $append;
            }
        }

        if (array_key_exists($modelName, $appends)) {
            foreach ($appends[$modelName] as $key) {
                $keys[] = $key;
            }
        }

        return $keys;
    }

    public function getFlexibleHiddenKeys($modelName, $relation = null)
    {
        if (!property_exists(get_called_class(), 'flexibleHidden')) {
            return [];
        }

        $hidden = $this->flexibleHidden;

        if (!empty($relation)) {
            if (!array_key_exists($relation, $hidden)) {
                return [];
            }

            $hidden = $hidden[$relation];
        }

        $keys = [];

        foreach ($hidden as $append) {
            if (!is_array($append)) {
                $keys[] = $append;
            }
        }

        if (array_key_exists($modelName, $hidden)) {
            foreach ($hidden[$modelName] as $key) {
                $keys[] = $key;
            }
        }

        return $keys;
    }

    public function getFlexibleOnlyKeys($modelName, $relation = null)
    {
        if (!property_exists(get_called_class(), 'flexibleOnly')) {
            return [];
        }

        $only = $this->flexibleOnly;

        if (!empty($relation)) {
            if (!array_key_exists($relation, $only)) {
                return [];
            }

            $only = $only[$relation];
        }

        $keys = [];

        foreach ($only as $append) {
            if (!is_array($append)) {
                $keys[] = $append;
            }
        }

        if (array_key_exists($modelName, $only)) {
            foreach ($only[$modelName] as $key) {
                $keys[] = $key;
            }
        }

        return $keys;
    }

    public function flexibleIsIndexable()
    {
        if (method_exists($this, 'trashed')) {
            return !$this->trashed();
        }
        return true;
    }

    public function getFlexibleIndexName()
    {
        $index_prefix = App::make('Menthol\Flexible\Config')->get('elasticsearch.index_prefix', '');
        if (isset($this->flexibleIndexName)) {
            return $index_prefix . $this->flexibleIndexName;
        }

        return $index_prefix . $this->getTable();
    }

    public function getFlexibleType()
    {
        if (isset($this->flexibleType)) {
            return $this->flexibleType;
        }

        return str_singular($this->getTable());
    }

    public function getFlexibleIndexSettings()
    {
        $params = App::make('Menthol\Flexible\Config')->get('elasticsearch.defaults', []);
        if (isset($this->flexibleIndexSettings)) {
            foreach ($this->flexibleIndexSettings as $analyzer => $fields) {
                foreach($fields as $field) {
                    $fieldPath = str_replace('.', '.properties.', $field);
                    array_set($params['mappings'][$this->getFlexibleType()]['properties'], $fieldPath, [
                        'fields' => [
                            'analyzed'   => [
                                'type' => 'string',
                            ],
                            $analyzer => [
                                'analyzer' => "flexible_{$analyzer}_index",
                                'type'     => 'string',
                            ],
                        ],
                        'index'  => 'not_analyzed',
                        'type'   => 'string',
                    ]);
                }
            }
        }

        return $params;
    }

}
