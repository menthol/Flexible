<?php namespace Menthol\Flexible\Traits;

use Illuminate\Support\Facades\App;

trait TransformableTrait
{

    /**
     * Transform the Person model and its relations to an Elasticsearch document.
     *
     * @param bool $relations
     * @param bool $forceReloadRelation
     * @return array
     */
    public function transform($relations = false, $forceReloadRelation = true)
    {
        $relations = $relations ? App::make('flexible.config')->get('paths.' . get_class($this)) : [];

        $loadedRelations = $this->getRelations();

        if ($forceReloadRelation && is_array($relations)) {
            $this->load($relations);
        } elseif (is_array($relations)) {
            foreach ($relations as $relation) {
                if (!array_key_exists($relation, $loadedRelations)) {
                    $this->load($relation);
                }
            }
        }

        return $this->toArray();
    }

}
