<?php

namespace Cheney\AdminSystem\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'articles';
    protected $fillable = [
        'title', 'slug', 'summary', 'content', 'cover_image', 
        'category_id', 'author_id', 'status', 'is_top', 'is_hot', 
        'is_recommend', 'view_count', 'like_count', 'comment_count', 
        'published_at'
    ];

    public function category()
    {
        return $this->belongsTo(ArticleCategory::class);
    }

    public function author()
    {
        return $this->belongsTo(Admin::class);
    }

    public function tags()
    {
        return $this->belongsToMany(ArticleTag::class, 'article_tag_relations', 'article_id', 'tag_id');
    }
}