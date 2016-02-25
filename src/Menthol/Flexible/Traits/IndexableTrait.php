<?php namespace Menthol\Flexible\Traits;

use Exception;
use Illuminate\Database\Eloquent\Model;

trait IndexableTrait
{
    use ObservableTrait;

    static public function bootIndexableTrait()
    {
        if (!new static instanceof Model) {
            throw new Exception('This trait can ony be used in Eloquent models.');
        }
    }

    static public function getFlexibleIndexRelationships()
    {
        $modelName = get_called_class();
        if (property_exists($modelName, 'flexibleIndexRelationships')) {
            return $modelName::$flexibleIndexRelationships;
        }

        return static::getFlexibleRelationships();
    }

    static public function getFlexibleAppendKeys($modelName, $relation = null)
    {
        if (!property_exists(get_called_class(), 'flexibleAppends')) {
            return [];
        }

        $appends = static::$flexibleAppends;

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

    static public function getFlexibleHiddenKeys($modelName, $relation = null)
    {
        if (!property_exists(get_called_class(), 'flexibleHidden')) {
            return [];
        }

        $appends = static::$flexibleHidden;

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
        $modelName = get_class($this);
        if (property_exists($modelName, 'flexibleIndexName')) {
            return $index_prefix . ($modelName::$flexibleIndexName);
        }

        return $index_prefix . $this->getTable();
    }

    public function getFlexibleType()
    {
        $modelName = get_class($this);
        if (property_exists($modelName, 'flexibleType')) {
            return ($modelName::$flexibleType);
        }

        return str_singular($this->getTable());
    }

}
