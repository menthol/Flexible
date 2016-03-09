<?php namespace Menthol\Flexible\Traits;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Menthol\Flexible\Observer;

trait ObservableTrait
{
    public $flexibleRelatedModels = [];

    static public function bootObservableTrait()
    {
        if (!new static instanceof Model) {
            throw new Exception('This trait can ony be used in Eloquent models.');
        }

        static::observe(new Observer);
    }

    public function getFlexibleObservedRelationships()
    {
        $relationships = [];

        if (method_exists($this, 'getFlexibleIndexRelationships'))
        {
            $relationships = $this->getFlexibleIndexRelationships();
        }

        if (isset($this->flexibleObservedRelationships)) {
            $relationships = array_merge($relationships, $this->flexibleObservedRelationships);
        }

        return array_unique($relationships);
    }


}
