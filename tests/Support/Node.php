<?php

namespace Menthol\Flexible\Tests;

use Illuminate\Database\Eloquent\Model;
use Menthol\Flexible\Traits\ObservableTrait;

/**
 * Menthol\Flexible\Tests\Node
 *
 * @property integer $id
 * @property integer $parent_id
 * @property string $name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Menthol\Flexible\Tests\Article[] $articles
 * @property-read \Menthol\Flexible\Tests\Node $parent
 * @property-read \Illuminate\Database\Eloquent\Collection|\Menthol\Flexible\Tests\Node[] $children
 */
class Node extends Model
{
    use ObservableTrait;

    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    public function parent()
    {
        return $this->belongsTo(Node::class);
    }

    public function children()
    {
        return $this->hasMany(Node::class, 'parent_id');
    }
}
