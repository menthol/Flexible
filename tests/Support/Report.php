<?php

namespace Menthol\Flexible\Tests;

use Illuminate\Database\Eloquent\Model;
use Menthol\Flexible\Traits\ObservableTrait;

/**
 * Menthol\Flexible\Tests\Report
 *
 * @property integer $id
 * @property integer $user_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Menthol\Flexible\Tests\User $user
 */
class Report extends Model
{
    use ObservableTrait;

    static public $flexibleRelationships = [
        'articles',
    ];

    public function getMd5Attribute()
    {
        return md5($this->getKey());
    }

    public function getSha1Attribute()
    {
        return sha1($this->getKey());
    }

    public function articles()
    {
        return $this->morphedByMany('Menthol\Flexible\Tests\Article', 'reportable');
    }

    public function authors()
    {
        return $this->morphedByMany('Menthol\Flexible\Tests\Author', 'reportable');
    }

    public function comments()
    {
        return $this->morphedByMany('Menthol\Flexible\Tests\Comment', 'reportable');
    }

    public function user()
    {
        return $this->belongsTo('Menthol\Flexible\Tests\User');
    }
}
