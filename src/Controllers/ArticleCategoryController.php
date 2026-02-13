<?php

namespace Cheney\AdminSystem\Controllers;

use Cheney\AdminSystem\Controllers\Controller;
use Illuminate\Http\Request;
use Cheney\AdminSystem\Services\ArticleCategoryService;
use Cheney\AdminSystem\Traits\ApiResponseTrait;
use OpenApi\Annotations as OA;

class ArticleCategoryController extends Controller
{
    use ApiResponseTrait;

    protected $categoryService;

    public function __construct(ArticleCategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * @OA\Get(
     *     path="/api/article-categories",
     *     summary="获取分类列表",
     *     description="获取文章分类列表",
     *     tags={"文章分类管理"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="页码",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="每页数量",
     *         required=false,
     *         @OA\Schema(type="integer", example=15)
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="名称搜索",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="状态：0-禁用，1-启用",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="获取成功",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=10000),
     *             @OA\Property(property="message", type="string", example="获取成功"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $categories = $this->categoryService->index($request->all());
            return $this->success($categories);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/article-categories/tree",
     *     summary="获取分类树",
     *     description="获取文章分类树形结构",
     *     tags={"文章分类管理"},
     *     security={{"bearer":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="获取成功",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=10000),
     *             @OA\Property(property="message", type="string", example="获取成功"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function tree()
    {
        try {
            $tree = $this->categoryService->tree();
            return $this->success($tree);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/article-categories",
     *     summary="创建分类",
     *     description="创建新文章分类",
     *     tags={"文章分类管理"},
     *     security={{"bearer":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="技术文章", description="分类名称"),
     *             @OA\Property(property="slug", type="string", example="tech", description="URL 别名"),
     *             @OA\Property(property="description", type="string", example="技术相关文章", description="分类描述"),
     *             @OA\Property(property="parent_id", type="integer", example=0, description="父分类 ID"),
     *             @OA\Property(property="sort_order", type="integer", example=1, description="排序"),
     *             @OA\Property(property="icon", type="string", example="book", description="分类图标"),
     *             @OA\Property(property="status", type="integer", example=1, description="状态：0-禁用，1-启用")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="创建成功",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=10000),
     *             @OA\Property(property="message", type="string", example="创建成功"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:100',
                'slug' => 'nullable|string|max:100|unique:article_categories,slug',
                'description' => 'nullable|string|max:500',
                'parent_id' => 'nullable|integer|min:0',
                'sort_order' => 'nullable|integer|min:0',
                'icon' => 'nullable|string|max:100',
                'status' => 'nullable|in:0,1'
            ]);

            $category = $this->categoryService->store($validated);
            return $this->created($category);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/article-categories/{id}",
     *     summary="获取分类详情",
     *     description="获取指定文章分类的详细信息",
     *     tags={"文章分类管理"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="分类ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="获取成功",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=10000),
     *             @OA\Property(property="message", type="string", example="获取成功"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $category = $this->categoryService->show($id);
            return $this->success($category);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * @OA\Put(
     *     path="/api/article-categories/{id}",
     *     summary="更新分类",
     *     description="更新指定文章分类的信息",
     *     tags={"文章分类管理"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="分类ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="技术文章（更新）"),
     *             @OA\Property(property="slug", type="string", example="tech"),
     *             @OA\Property(property="description", type="string", example="技术相关文章"),
     *             @OA\Property(property="parent_id", type="integer", example=0),
     *             @OA\Property(property="sort_order", type="integer", example=1),
     *             @OA\Property(property="icon", type="string", example="book"),
     *             @OA\Property(property="status", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="更新成功",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=10000),
     *             @OA\Property(property="message", type="string", example="更新成功"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:100',
                'slug' => 'nullable|string|max:100|unique:article_categories,slug,' . $id,
                'description' => 'nullable|string|max:500',
                'parent_id' => 'nullable|integer|min:0',
                'sort_order' => 'nullable|integer|min:0',
                'icon' => 'nullable|string|max:100',
                'status' => 'nullable|in:0,1'
            ]);

            $category = $this->categoryService->update($id, $validated);
            return $this->success($category, '更新成功');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/article-categories/{id}",
     *     summary="删除分类",
     *     description="删除指定文章分类",
     *     tags={"文章分类管理"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="分类ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="删除成功",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=10000),
     *             @OA\Property(property="message", type="string", example="删除成功"),
     *             @OA\Property(property="data", type="object", nullable=true)
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            $this->categoryService->destroy($id);
            return $this->success(null, '删除成功');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}