<?php

namespace Cheney\AdminSystem\Services;

use Cheney\AdminSystem\Models\ArticleTag;

class ArticleTagService
{
    protected $tagModel;

    public function __construct(ArticleTag $tagModel)
    {
        $this->tagModel = $tagModel;
    }

    /**
     * 获取文章标签列表
     * 
     * @param array $params 查询参数，支持 name（名称）、page（页码）、per_page（每页数量）
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator 返回分页的标签列表
     */
    public function index(array $params = [])
    {
        $query = $this->tagModel->query();

        if (isset($params['name'])) {
            $query->where('name', 'like', '%' . $params['name'] . '%');
        }

        $page = $params['page'] ?? 1;
        $perPage = $params['per_page'] ?? 15;

        return $query->orderBy('created_at', 'desc')->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * 获取文章标签详情
     * 
     * @param int $id 标签ID
     * @return ArticleTag 返回标签模型实例
     */
    public function show(int $id)
    {
        return $this->tagModel->findOrFail($id);
    }

    /**
     * 创建文章标签
     * 
     * @param array $data 标签数据，包含 name（名称）、slug（别名）、color（颜色）等
     * @return ArticleTag 返回创建的标签模型实例
     */
    public function store(array $data): ArticleTag
    {
        return $this->tagModel->create($data);
    }

    /**
     * 更新文章标签
     * 
     * @param int $id 标签ID
     * @param array $data 标签数据，包含 name（名称）、slug（别名）、color（颜色）等
     * @return ArticleTag 返回更新后的标签模型实例
     */
    public function update(int $id, array $data): ArticleTag
    {
        $tag = $this->tagModel->findOrFail($id);
        $tag->update($data);
        return $tag->fresh();
    }

    /**
     * 删除文章标签
     * 
     * @param int $id 标签ID
     * @return bool 删除成功返回true，失败返回false
     * @throws \Exception 如果标签下有文章，抛出异常
     */
    public function destroy(int $id): bool
    {
        $tag = $this->tagModel->findOrFail($id);

        if ($tag->articles()->exists()) {
            throw new \Exception('该标签下有文章，无法删除');
        }

        return $tag->delete();
    }
}