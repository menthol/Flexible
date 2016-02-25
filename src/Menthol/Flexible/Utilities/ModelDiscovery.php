<?php namespace Menthol\Flexible\Utilities;

use Symfony\Component\ClassLoader\ClassMapGenerator;

class ModelDiscovery
{

    static public function discover($directory, $models = [], $withTrait = '')
    {

        foreach (ClassMapGenerator::createMap($directory) as $model => $path) {
            $class_parents = @class_parents($model);
            if ($class_parents && in_array('Illuminate\Database\Eloquent\Model', $class_parents, true)) {
                if (!$withTrait || in_array($withTrait, class_uses_recursive($model))) {
                    $models[] = $model;
                }
            }
        }

        return $models;
    }
}
