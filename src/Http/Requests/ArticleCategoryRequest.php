<?php

namespace Cheney\AdminSystem\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:100',
            'slug' => 'nullable|string|max:100|unique:article_categories,slug,' . $this->route('id'),
            'description' => 'nullable|string|max:500',
            'parent_id' => 'nullable|exists:article_categories,id',
            'sort_order' => 'nullable|integer',
            'icon' => 'nullable|string|max:100',
            'status' => 'nullable|in:0,1'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '分类名称不能为空',
            'parent_id.exists' => '父分类不存在'
        ];
    }
}