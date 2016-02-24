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

    public function flexibleRefreshDoc()
    {

    }

}
