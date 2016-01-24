<?php namespace Menthol\Flexible\Traits;

use Illuminate\Database\Eloquent\Model;
use Menthol\Flexible\Observer;

trait CallableTrait
{

    /**
     * Boot the trait by registering the Flexible observer with the model
     */
    public static function bootCallableTrait()
    {
        if (new static instanceof Model) {
            static::observe(new Observer);
        } else {
            throw new \Exception("This trait can ony be used in Eloquent models.");
        }
    }

}
