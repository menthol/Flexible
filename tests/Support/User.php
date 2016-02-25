<?php

namespace Menthol\Flexible\Tests;

use Illuminate\Database\Eloquent\Model;
use Menthol\Flexible\Traits\ObservableTrait;

/**
 * Menthol\Flexible\Tests\User
 *
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Menthol\Flexible\Tests\Author $author
 * @property-read \Illuminate\Database\Eloquent\Collection|\Menthol\Flexible\Tests\Comment[] $comments
 * @property-read \Illuminate\Database\Eloquent\Collection|\Menthol\Flexible\Tests\Like[] $likes
 * @property-read \Illuminate\Database\Eloquent\Collection|\Menthol\Flexible\Tests\Report[] $reports
 * @property-read \Illuminate\Database\Eloquent\Collection|\Menthol\Flexible\Tests\Review[] $reviews
 */
class User extends Model
{
    use ObservableTrait;

    public function author()
    {
        return $this->hasOne('Menthol\Flexible\Tests\Author');
    }

    public function comments()
    {
        return $this->hasMany('Menthol\Flexible\Tests\Comment');
    }

    public function likes()
    {
        return $this->hasMany('Menthol\Flexible\Tests\Like');
    }

    public function reports()
    {
        return $this->hasMany('Menthol\Flexible\Tests\Report');
    }

    public function reviews()
    {
        return $this->hasMany('Menthol\Flexible\Tests\Review');
    }
}
