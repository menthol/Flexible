<?php

namespace Menthol\Flexible\Tests;

use Illuminate\Database\Eloquent\Model;
use Menthol\Flexible\Traits\ObservableTrait;

/**
 * Menthol\Flexible\Tests\Comment
 *
 * @property integer $id
 * @property integer $article_id
 * @property integer $user_id
 * @property string $body
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Menthol\Flexible\Tests\Article $article
 * @property-read \Illuminate\Database\Eloquent\Collection|\Menthol\Flexible\Tests\Like[] $likes
 * @property-read \Illuminate\Database\Eloquent\Collection|\Menthol\Flexible\Tests\Report[] $reports
 * @property-read \Menthol\Flexible\Tests\User $user
 */
class Comment extends Model
{
    use ObservableTrait;

    public function article()
    {
        return $this->belongsTo('Menthol\Flexible\Tests\Article');
    }

    public function likes()
    {
        return $this->morphMany('Menthol\Flexible\Tests\Like', 'likeable');
    }

    public function reports()
    {
        return $this->morphToMany('Menthol\Flexible\Tests\Report', 'reportable');
    }

    public function review()
    {
        return $this->morphOne('Menthol\Flexible\Tests\Review', 'reviewable');
    }

    public function user()
    {
        return $this->belongsTo('Menthol\Flexible\Tests\User');
    }

}
