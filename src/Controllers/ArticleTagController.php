<?php

namespace Cheney\AdminSystem\Controllers;

use Cheney\AdminSystem\Controllers\Controller;
use Illuminate\Http\Request;
use Cheney\AdminSystem\Services\ArticleTagService;
use Cheney\AdminSystem\Traits\ApiResponseTrait;
use OpenApi\Annotations as OA;

class ArticleTagController extends Controller
{
    use ApiResponseTrait;

    protected $tagService;

    public function __construct(ArticleTagService $tagService)
    {
        $this->tagService = $tagService;
    }

    /**
     * @OA\Get(
     *     path="/api/article-tags",
     *     summary="获取标签列表",
     *     description="获取文章标签列表",
     *     tags={"文章标签管理"},
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
            $tags = $this->tagService->index($request->all());
            return $this->success($tags);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/article-tags",
     *     summary="创建标签",
     *     description="创建新文章标签",
     *     tags={"文章标签管理"},
     *     security={{"bearer":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="PHP", description="标签名称"),
     *             @OA\Property(property="slug", type="string", example="php", description="URL 别名"),
     *             @OA\Property(property="color", type="string", example="#1890ff", description="标签颜色")
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
                'name' => 'required|string|max:50',
                'slug' => 'nullable|string|max:50|unique:article_tags,slug',
                'color' => 'nullable|string|max:20'
            ]);

            $tag = $this->tagService->store($validated);
            return $this->created($tag);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/article-tags/{id}",
     *     summary="获取标签详情",
     *     description="获取指定文章标签的详细信息",
     *     tags={"文章标签管理"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="标签ID",
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
            $tag = $this->tagService->show($id);
            return $this->success($tag);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * @OA\Put(
     *     path="/api/article-tags/{id}",
     *     summary="更新标签",
     *     description="更新指定文章标签的信息",
     *     tags={"文章标签管理"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="标签ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="PHP（更新）"),
     *             @OA\Property(property="slug", type="string", example="php"),
     *             @OA\Property(property="color", type="string", example="#1890ff")
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
                'name' => 'required|string|max:50',
                'slug' => 'nullable|string|max:50|unique:article_tags,slug,' . $id,
                'color' => 'nullable|string|max:20'
            ]);

            $tag = $this->tagService->update($id, $validated);
            return $this->success($tag, '更新成功');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/article-tags/{id}",
     *     summary="删除标签",
     *     description="删除指定文章标签",
     *     tags={"文章标签管理"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="标签ID",
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
            $this->tagService->destroy($id);
            return $this->success(null, '删除成功');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}