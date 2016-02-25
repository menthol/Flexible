<?php

namespace Menthol\Flexible\Tests;

use Illuminate\Database\Eloquent\Model;
use Menthol\Flexible\Traits\IndexableTrait;

/**
 * Menthol\Flexible\Tests\Author
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Menthol\Flexible\Tests\Article[] $articles
 * @property-read \Illuminate\Database\Eloquent\Collection|\Menthol\Flexible\Tests\Like[] $likes
 * @property-read \Illuminate\Database\Eloquent\Collection|\Menthol\Flexible\Tests\Report[] $reports
 * @property-read \Menthol\Flexible\Tests\User $user
 */
class Author extends Model
{
    use IndexableTrait;

    public function getMd5Attribute()
    {
        return md5($this->getKey());
    }

    public function articles()
    {
        return $this->hasMany('Menthol\Flexible\Tests\Article');
    }

    public function comments()
    {
        return $this->hasManyThrough('Menthol\Flexible\Tests\Comment', 'Menthol\Flexible\Tests\Article');
    }

    public function likes()
    {
        return $this->morphMany('Menthol\Flexible\Tests\Like', 'likeable');
    }

    public function reports()
    {
        return $this->morphToMany('Menthol\Flexible\Tests\Report', 'reportable');
    }

    public function user()
    {
        return $this->belongsTo('Menthol\Flexible\Tests\User');
    }

}
