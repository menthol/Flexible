<?php

use Menthol\Flexible\Traits\SearchableTrait;

class Husband extends Illuminate\Database\Eloquent\Model
{

    use SearchableTrait;

    public static $__es_config = [
        'autocomplete' => ['name', 'wife.name'],

        'suggest' => ['name'],

        'text_start' => ['name', 'wife.children.name'],
        'text_middle' => ['name', 'wife.children.name'],
        'text_end' => ['name', 'wife.children.name'],

        'word_start' => ['name', 'wife.children.name'],
        'word_middle' => ['name', 'wife.children.name'],
        'word_end' => ['name', 'wife.children.name']
    ];

    /**
     * @follow UNLESS Toy
     * @follow UNLESS Child
     *
     * @return \Illuminate\Database\Eloquent\Relations
     */
    public function wife()
    {
        return $this->hasOne('Wife');
    }

    /**
     * @follow NEVER
     *
     * @return \Illuminate\Database\Eloquent\Relations
     */
    public function children()
    {
        return $this->hasMany('Child', 'father_id');
    }

    public function getEsId()
    {
        return 'dummy_id';
    }

}
