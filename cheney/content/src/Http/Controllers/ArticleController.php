<?php

namespace Cheney\Content\Http\Controllers;

use Illuminate\Http\Request;
use Cheney\Content\Http\Services\ArticleService;
use Illuminate\Support\Facades\Validator;

class ArticleController extends Controller
{
    /**
     * @var ArticleService
     */
    private $service;

    public function __construct(ArticleService $articleService)
    {
        $this->service = $articleService;
    }

    /**
     * 获取内容列表
     *
     * * @OA\Get(
     *  tags={"内容"},
     *  path="/api/frontend/article/list",
     *  operationId="articleList",
     *  description="内容列表",
     *  @OA\Parameter(name="TypeId",in="query",description="分类id"),
     *  @OA\Parameter(name="page",in="query",description="当前页"),
     *  @OA\Parameter(name="page_num",in="query",description="每页条数",),
     *  @OA\Response(response="100000", description="success"),
     *  @OA\Response(response="200000", description="fail"),
     * )
     * @param Request $request
     * @return string
     */
    public function lists(Request $request)
    {
        $inputs = $request->only('TypeId','page','page_num');
        $validator = Validator::make($inputs, [
            'Option'     => ['required']
        ]);
        if ($validator->fails()) {
            return self::parametersIllegal($validator->messages()->first());
        }
        try{
            $inputs['page_limit'] = isset($inputs['page_num']) ? $inputs['page_num'] : 10;
            $result = $this->service ->getArticleList($inputs);
            if(!$result){
                return self::parametersIllegal("没有数据");
            }
            return self::success($result);
        }catch (\Exception $e){
            return self::error($e->getCode(),$e->getMessage());
        }
    }

    /**
     * 获取文章列表
     *
     * @OA\Get(
     *   tags={"内容"},
     *   path="/api/frontend/article/tops",
     *   operationId="articleTops",
     *   description="文章推荐列表",
     *   @OA\Parameter(name="Option",in="query",description="内容选项【TOPIC：话题，ARTICLE：文章】",@OA\Schema(type="string")),
     *   @OA\Response(response="100000", description="success"),
     *   @OA\Response(response="200000", description="fail"),
     * )
     * @param Request $request
     * @return string
     */
    public function tops(Request $request)
    {
        $inputs = $request->only('Option');
        $validator = Validator::make($inputs, [
            'Option'     => ['required']
        ]);
        if ($validator->fails()) {
            return self::parametersIllegal($validator->messages()->first());
        }
        try{
            $inputs['IsTop'] = 1;
            $result = $this->service ->getArticleList($inputs);
            if(!$result){
                return self::parametersIllegal("没有数据");
            }
            return self::success($result);
        }catch (\Exception $e){
            return self::error($e->getCode(),$e->getMessage());
        }
    }

    /**
     * 获取文章详情
     *
     * @OA\Get(
     *   tags={"内容"},
     *   path="/api/frontend/article/detail/{id}",
     *   operationId="articleDetail",
     *   description="获取内容详情",
     *   @OA\Parameter(name="id",in="path",description="内容id",@OA\Schema(type="int")),
     *   @OA\Response(response="100000", description="success"),
     *   @OA\Response(response="200000", description="fail"),
     * )
     * @param Request $request
     * @return string
     */
    public function detail($id)
    {
        try{
            $result = $this->service ->getById($id);
            dd($result->toArray());
            if(!$result){
                return self::parametersIllegal("没有数据");
            }
            return self::success($result);
        }catch (\Exception $e){
            return self::error($e->getCode(),$e->getMessage());
        }
    }
}
