<?php namespace Menthol\Flexible\Traits;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Menthol\Flexible\Observer;

trait ObservableTrait
{
    static public function bootObservableTrait()
    {
        if (!new static instanceof Model) {
            throw new Exception('This trait can ony be used in Eloquent models.');
        }

        static::observe(new Observer(static::class));
    }

    static public function getFlexibleRelationships()
    {
        $modelName = static::class;
        if (property_exists($modelName, 'flexibleRelationships')) {
            return $modelName::$flexibleRelationships;
        }

        return [];
    }


}
