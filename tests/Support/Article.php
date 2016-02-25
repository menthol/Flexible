<?php

namespace Menthol\Flexible\Tests;

use Illuminate\Database\Eloquent\Model;
use Menthol\Flexible\Traits\IndexableTrait;

/**
 * Menthol\Flexible\Tests\Article
 *
 * @property integer $id
 * @property integer $node_id
 * @property integer $author_id
 * @property string $title
 * @property string $body
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Menthol\Flexible\Tests\Author $author
 * @property-read \Illuminate\Database\Eloquent\Collection|\Menthol\Flexible\Tests\Comment[] $comments
 * @property-read \Illuminate\Database\Eloquent\Collection|\Menthol\Flexible\Tests\Like[] $likes
 * @property-read \Menthol\Flexible\Tests\Node $node
 * @property-read \Illuminate\Database\Eloquent\Collection|\Menthol\Flexible\Tests\Report[] $reports
 * @property-read \Illuminate\Database\Eloquent\Collection|\Menthol\Flexible\Tests\Tag[] $tags
 */
class Article extends Model
{
    use IndexableTrait;

    static public $flexibleRelationships = [
        'author',
        'author.user.reports',
        'comments',
        'likes',
        'node',
        'tags',
    ];

    static public $flexibleAppends = [
        'md5',
        'author' => [
            'md5'
        ],
        'author.user.reports' => [
            'sha1',
            'Menthol\Flexible\Tests\Comment' => 'md5',
        ],
    ];

    static public $flexibleHidden = [
        'author.user' => [
            'password',
        ],
    ];

    public function getMd5Attribute()
    {
        return md5($this->getKey());
    }

    public function author()
    {
        return $this->belongsTo('Menthol\Flexible\Tests\Author');
    }

    public function comments()
    {
        return $this->hasMany('Menthol\Flexible\Tests\Comment');
    }

    public function likes()
    {
        return $this->morphMany('Menthol\Flexible\Tests\Like', 'likeable');
    }

    public function node()
    {
        return $this->belongsTo('Menthol\Flexible\Tests\Node');
    }

    public function reports()
    {
        return $this->morphToMany('Menthol\Flexible\Tests\Report', 'reportable');
    }

    public function review()
    {
        return $this->morphOne('Menthol\Flexible\Tests\Review', 'reviewable');
    }

    public function tags()
    {
        return $this->belongsToMany('Menthol\Flexible\Tests\Tag');
    }
}
