<?php

namespace Cheney\AdminSystem\Controllers;

use Cheney\AdminSystem\Controllers\Controller;
use Illuminate\Http\Request;
use Cheney\AdminSystem\Services\ArticleService;
use Cheney\AdminSystem\Traits\ApiResponseTrait;
use OpenApi\Annotations as OA;

class ArticleController extends Controller
{
    use ApiResponseTrait;

    protected $articleService;

    public function __construct(ArticleService $articleService)
    {
        $this->articleService = $articleService;
    }

    /**
     * @OA\Get(
     *     path="/api/articles",
     *     summary="获取文章列表",
     *     description="获取文章列表，支持分页和搜索",
     *     tags={"文章管理"},
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
     *         name="title",
     *         in="query",
     *         description="标题搜索",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         description="分类 ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="tag_id",
     *         in="query",
     *         description="标签 ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="状态：0-草稿，1-已发布，2-已下架",
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
            $articles = $this->articleService->index($request->all());
            return $this->success($articles);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/articles",
     *     summary="创建文章",
     *     description="创建新文章",
     *     tags={"文章管理"},
     *     security={{"bearer":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "content"},
     *             @OA\Property(property="title", type="string", example="文章标题", description="文章标题"),
     *             @OA\Property(property="slug", type="string", example="article-slug", description="URL 别名"),
     *             @OA\Property(property="summary", type="string", example="文章摘要", description="文章摘要"),
     *             @OA\Property(property="content", type="string", example="文章内容", description="文章内容"),
     *             @OA\Property(property="cover_image", type="string", example="/uploads/cover.jpg", description="封面图片"),
     *             @OA\Property(property="category_id", type="integer", example=1, description="分类 ID"),
     *             @OA\Property(property="tag_ids", type="array", @OA\Items(type="integer"), example={1, 2}, description="标签 ID 数组"),
     *             @OA\Property(property="status", type="integer", example=0, description="状态：0-草稿，1-已发布，2-已下架"),
     *             @OA\Property(property="is_top", type="boolean", example=false, description="是否置顶"),
     *             @OA\Property(property="is_hot", type="boolean", example=false, description="是否热门"),
     *             @OA\Property(property="is_recommend", type="boolean", example=false, description="是否推荐"),
     *             @OA\Property(property="published_at", type="string", format="date-time", example="2026-02-12T10:00:00Z", description="发布时间")
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
            // 处理FormData中的JSON字符串字段
            $requestData = $request->all();
            
            // 如果tag_ids是JSON字符串，解码为数组
            if (isset($requestData['tag_ids']) && is_string($requestData['tag_ids'])) {
                $request->merge(['tag_ids' => json_decode($requestData['tag_ids'], true)]);
            }
            
            // 处理布尔类型字段
            $booleanFields = ['is_top', 'is_hot', 'is_recommend'];
            foreach ($booleanFields as $field) {
                if (isset($requestData[$field])) {
                    $request->merge([$field => filter_var($requestData[$field], FILTER_VALIDATE_BOOLEAN)]);
                }
            }
            
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'slug' => 'nullable|string|max:255|unique:articles,slug',
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
            ]);

            $validated['author_id'] = auth('admin')->id();

            $article = $this->articleService->store($validated);
            return $this->created($article);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/articles/{id}",
     *     summary="获取文章详情",
     *     description="获取指定文章的详细信息",
     *     tags={"文章管理"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="文章ID",
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
            $article = $this->articleService->show($id);
            return $this->success($article);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * @OA\Put(
     *     path="/api/articles/{id}",
     *     summary="更新文章",
     *     description="更新指定文章的信息",
     *     tags={"文章管理"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="文章ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="文章标题（更新）"),
     *             @OA\Property(property="slug", type="string", example="article-slug"),
     *             @OA\Property(property="summary", type="string", example="文章摘要"),
     *             @OA\Property(property="content", type="string", example="文章内容"),
     *             @OA\Property(property="cover_image", type="string", example="/uploads/cover.jpg"),
     *             @OA\Property(property="category_id", type="integer", example=1),
     *             @OA\Property(property="tag_ids", type="array", @OA\Items(type="integer"), example={1, 2}),
     *             @OA\Property(property="status", type="integer", example=1),
     *             @OA\Property(property="is_top", type="boolean", example=true),
     *             @OA\Property(property="is_hot", type="boolean", example=true),
     *             @OA\Property(property="is_recommend", type="boolean", example=true),
     *             @OA\Property(property="published_at", type="string", format="date-time", example="2026-02-12T10:00:00Z")
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
            // 处理FormData中的JSON字符串字段
            $requestData = $request->all();
            
            // 如果tag_ids是JSON字符串，解码为数组
            if (isset($requestData['tag_ids']) && is_string($requestData['tag_ids'])) {
                $request->merge(['tag_ids' => json_decode($requestData['tag_ids'], true)]);
            }
            
            // 处理布尔类型字段
            $booleanFields = ['is_top', 'is_hot', 'is_recommend'];
            foreach ($booleanFields as $field) {
                if (isset($requestData[$field])) {
                    $request->merge([$field => filter_var($requestData[$field], FILTER_VALIDATE_BOOLEAN)]);
                }
            }
            
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'slug' => 'nullable|string|max:255|unique:articles,slug,' . $id,
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
            ]);

            $article = $this->articleService->update($id, $validated);
            return $this->success($article, '更新成功');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/articles/{id}",
     *     summary="删除文章",
     *     description="删除指定文章",
     *     tags={"文章管理"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="文章ID",
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
            $this->articleService->destroy($id);
            return $this->success(null, '删除成功');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/articles/{id}/publish",
     *     summary="发布文章",
     *     description="发布指定文章",
     *     tags={"文章管理"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="文章ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="发布成功",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=10000),
     *             @OA\Property(property="message", type="string", example="发布成功"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function publish($id)
    {
        try {
            $article = $this->articleService->publish($id);
            return $this->success($article, '发布成功');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/articles/{id}/unpublish",
     *     summary="下架文章",
     *     description="下架指定文章",
     *     tags={"文章管理"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="文章ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="下架成功",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=10000),
     *             @OA\Property(property="message", type="string", example="下架成功"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function unpublish($id)
    {
        try {
            $article = $this->articleService->update($id, ['status' => 2]);
            return $this->success($article, '下架成功');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}