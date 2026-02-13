<?php

namespace Cheney\AdminSystem\Services;

use Cheney\AdminSystem\Models\ArticleCategory;

class ArticleCategoryService
{
    protected $categoryModel;

    public function __construct(ArticleCategory $categoryModel)
    {
        $this->categoryModel = $categoryModel;
    }

    /**
     * 获取文章分类列表
     * 
     * @param array $params 查询参数，支持 name（名称）、status（状态）、page（页码）、per_page（每页数量）
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator 返回分页的分类列表
     */
    public function index(array $params = [])
    {
        $query = $this->categoryModel->query();

        if (isset($params['name'])) {
            $query->where('name', 'like', '%' . $params['name'] . '%');
        }

        if (isset($params['status'])) {
            $query->where('status', $params['status']);
        }

        $page = $params['page'] ?? 1;
        $perPage = $params['per_page'] ?? 15;

        return $query->orderBy('sort_order')->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * 获取文章分类树
     * 
     * @return array 返回树形结构的分类列表
     */
    public function tree()
    {
        $categories = $this->categoryModel->orderBy('sort_order')->get();
        return $this->buildTree($categories->toArray());
    }

    /**
     * 获取文章分类详情
     * 
     * @param int $id 分类ID
     * @return ArticleCategory 返回分类模型实例
     */
    public function show(int $id)
    {
        return $this->categoryModel->findOrFail($id);
    }

    /**
     * 创建文章分类
     * 
     * @param array $data 分类数据，包含 name（名称）、slug（别名）、description（描述）、parent_id（父分类ID）等
     * @return ArticleCategory 返回创建的分类模型实例
     */
    public function store(array $data): ArticleCategory
    {
        return $this->categoryModel->create($data);
    }

    /**
     * 更新文章分类
     * 
     * @param int $id 分类ID
     * @param array $data 分类数据，包含 name（名称）、slug（别名）、description（描述）、parent_id（父分类ID）等
     * @return ArticleCategory 返回更新后的分类模型实例
     */
    public function update(int $id, array $data): ArticleCategory
    {
        $category = $this->categoryModel->findOrFail($id);
        $category->update($data);
        return $category->fresh();
    }

    /**
     * 删除文章分类
     * 
     * @param int $id 分类ID
     * @return bool 删除成功返回true，失败返回false
     * @throws \Exception 如果分类下有子分类或文章，抛出异常
     */
    public function destroy(int $id): bool
    {
        $category = $this->categoryModel->findOrFail($id);

        if ($category->children()->exists()) {
            throw new \Exception('该分类下有子分类，无法删除');
        }

        if ($category->articles()->exists()) {
            throw new \Exception('该分类下有文章，无法删除');
        }

        return $category->delete();
    }

    /**
     * 构建分类树
     * 
     * @param array $elements 分类数组
     * @param int $parentId 父分类ID，默认为0（顶级分类）
     * @return array 返回树形结构的分类列表
     */
    protected function buildTree(array $elements, $parentId = 0)
    {
        $branch = [];

        foreach ($elements as $element) {
            if ($element['parent_id'] == $parentId) {
                $children = $this->buildTree($elements, $element['id']);
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[] = $element;
            }
        }

        return $branch;
    }
}