<?php

namespace Cheney\AdminSystem\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleTagRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:50',
            'slug' => 'nullable|string|max:50|unique:article_tags,slug,' . $this->route('id'),
            'color' => 'nullable|string|max:20'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '标签名称不能为空'
        ];
    }
}