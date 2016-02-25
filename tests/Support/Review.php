<?php

namespace Menthol\Flexible\Tests;

use Illuminate\Database\Eloquent\Model;
use Menthol\Flexible\Traits\ObservableTrait;

/**
 * Menthol\Flexible\Tests\Review
 *
 * @property integer $id
 * @property integer $reviewable_id
 * @property string $reviewable_type
 * @property integer $user_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Menthol\Flexible\Tests\Review $reviewable
 * @property-read \Menthol\Flexible\Tests\User $user
 */
class Review extends Model
{
    use ObservableTrait;

    public function reviewable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo('Menthol\Flexible\Tests\User');
    }
}
