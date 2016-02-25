<?php namespace Menthol\Flexible\Traits;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

trait IndexableTrait
{
    use ObservableTrait;

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

        $appends = $this->flexibleHidden;

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
        return App::make('Menthol\Flexible\Config')->get('elasticsearch.defaults', []);
    }

}
