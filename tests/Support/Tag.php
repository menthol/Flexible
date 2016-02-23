<?php

namespace Menthol\Flexible\Tests;

use Illuminate\Database\Eloquent\Model;
use Menthol\Flexible\Traits\ObservableTrait;

/**
 * Menthol\Flexible\Tests\Tag
 *
 * @property integer $id
 * @property string $name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Menthol\Flexible\Tests\Article[] $articles
 */
class Tag extends Model
{
    use ObservableTrait;

    public function articles()
    {
        return $this->belongsToMany(Article::class);
    }
}
