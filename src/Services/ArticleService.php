<?php

namespace Cheney\AdminSystem\Services;

use Cheney\AdminSystem\Models\Article;
use Cheney\AdminSystem\Models\ArticleTag;

class ArticleService
{
    protected $articleModel;

    public function __construct(Article $articleModel)
    {
        $this->articleModel = $articleModel;
    }

    /**
     * 获取文章列表
     * 
     * @param array $params 查询参数，支持 title（标题）、category_id（分类ID）、tag_id（标签ID）、status（状态）、start_date（开始日期）、end_date（结束日期）、page（页码）、per_page（每页数量）
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator 返回分页的文章列表
     */
    public function index(array $params = [])
    {
        $query = $this->articleModel->with(['category', 'author', 'tags']);

        if (isset($params['title'])) {
            $query->where('title', 'like', '%' . $params['title'] . '%');
        }

        if (isset($params['category_id'])) {
            $query->where('category_id', $params['category_id']);
        }

        if (isset($params['tag_id'])) {
            $query->whereHas('tags', function ($q) use ($params) {
                $q->where('id', $params['tag_id']);
            });
        }

        if (isset($params['status'])) {
            $query->where('status', $params['status']);
        }

        if (isset($params['start_date'])) {
            $query->where('created_at', '>=', $params['start_date']);
        }

        if (isset($params['end_date'])) {
            $query->where('created_at', '<=', $params['end_date']);
        }

        $page = $params['page'] ?? 1;
        $perPage = $params['per_page'] ?? 15;

        return $query->orderBy('created_at', 'desc')->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * 获取文章详情
     * 
     * @param int $id 文章ID
     * @return Article 返回文章模型实例，包含分类、作者和标签关联数据
     */
    public function show(int $id)
    {
        return $this->articleModel->with(['category', 'author', 'tags'])->findOrFail($id);
    }

    /**
     * 创建文章
     * 
     * @param array $data 文章数据，包含 title（标题）、content（内容）、category_id（分类ID）、tag_ids（标签ID数组）等
     * @return Article 返回创建的文章模型实例，包含标签关联数据
     */
    public function store(array $data): Article
    {
        $tagIds = $data['tag_ids'] ?? [];
        unset($data['tag_ids']);

        $article = $this->articleModel->create($data);

        if (!empty($tagIds)) {
            $article->tags()->attach($tagIds);
        }

        return $article->load('tags');
    }

    /**
     * 更新文章
     * 
     * @param int $id 文章ID
     * @param array $data 文章数据，包含 title（标题）、content（内容）、category_id（分类ID）、tag_ids（标签ID数组）等
     * @return Article 返回更新后的文章模型实例，包含标签关联数据
     */
    public function update(int $id, array $data): Article
    {
        $article = $this->articleModel->findOrFail($id);
        
        // 只有当tag_ids字段存在时才进行标签同步
        if (isset($data['tag_ids'])) {
            $tagIds = $data['tag_ids'] ?? [];
            unset($data['tag_ids']);
        }

        $article->update($data);
        
        // 只有当tag_ids字段存在时才进行标签同步
        if (isset($tagIds)) {
            $article->tags()->sync($tagIds);
        }

        return $article->fresh()->load('tags');
    }

    /**
     * 删除文章
     * 
     * @param int $id 文章ID
     * @return bool 删除成功返回true，失败返回false
     */
    public function destroy(int $id): bool
    {
        $article = $this->articleModel->findOrFail($id);
        return $article->delete();
    }

    /**
     * 发布文章
     * 
     * @param int $id 文章ID
     * @return Article 返回更新后的文章模型实例
     */
    public function publish(int $id): Article
    {
        $article = $this->articleModel->findOrFail($id);
        $article->update([
            'status' => 1,
            'published_at' => now()
        ]);
        return $article->fresh();
    }

    /**
     * 取消发布文章
     * 
     * @param int $id 文章ID
     * @return Article 返回更新后的文章模型实例
     */
    public function unpublish(int $id): Article
    {
        $article = $this->articleModel->findOrFail($id);
        $article->update(['status' => 2]);
        return $article->fresh();
    }
}