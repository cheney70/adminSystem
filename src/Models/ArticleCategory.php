<?php

namespace Cheney\AdminSystem\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArticleCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'article_categories';
    protected $fillable = [
        'name', 'slug', 'description', 'parent_id', 
        'sort_order', 'icon', 'status', 'article_count'
    ];

    public function parent()
    {
        return $this->belongsTo(ArticleCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ArticleCategory::class, 'parent_id');
    }

    public function articles()
    {
        return $this->hasMany(Article::class);
    }
}