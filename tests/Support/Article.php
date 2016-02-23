<?php

namespace Menthol\Flexible\Tests;

use Illuminate\Database\Eloquent\Model;
use Menthol\Flexible\Traits\IndexableTrait;
use Menthol\Flexible\Traits\ObservableTrait;

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

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function node()
    {
        return $this->belongsTo(Node::class);
    }

    public function reports()
    {
        return $this->morphToMany(Report::class, 'reportable');
    }

    public function review()
    {
        return $this->morphOne(Review::class, 'reviewable');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
