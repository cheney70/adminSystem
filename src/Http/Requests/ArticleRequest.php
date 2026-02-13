<?php

namespace Cheney\AdminSystem\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:articles,slug,' . $this->route('id'),
            'summary' => 'nullable|string',
            'content' => 'required|string',
            'cover_image' => 'nullable|string|max:500',
            'category_id' => 'nullable|exists:article_categories,id',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'exists:article_tags,id',
            'status' => 'nullable|in:0,1,2',
            'is_top' => 'nullable|boolean',
            'is_hot' => 'nullable|boolean',
            'is_recommend' => 'nullable|boolean',
            'published_at' => 'nullable|date'
        ];
    }

    public function messages()
    {
        return [
            'title.required' => '标题不能为空',
            'content.required' => '内容不能为空',
            'category_id.exists' => '分类不存在',
            'tag_ids.*.exists' => '标签不存在'
        ];
    }
}