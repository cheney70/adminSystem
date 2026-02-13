<?php

namespace Cheney\AdminSystem\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArticleTag extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'article_tags';
    protected $fillable = [
        'name', 'slug', 'color', 'article_count'
    ];

    public function articles()
    {
        return $this->belongsToMany(Article::class, 'article_tag_relations', 'tag_id', 'article_id');
    }
}