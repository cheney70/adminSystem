<?php

namespace Cheney\Content\Http\Controllers;

use Illuminate\Http\Request;
use Cheney\Content\Http\ArticleTypeService;
use Illuminate\Support\Facades\Validator;

class ArticleTypeController extends Controller
{
    /**
     * @var ArticleTypeService
     */
    private $service;

    public function __construct(ArticleTypeService $articleService)
    {
        $this->service = $articleService;
    }

    /**
     * 获取内容分类列表
     *
     * @OA\Get(
     *   tags={"内容分类"},
     *   path="/api/frontend/article-type/list",
     *   operationId="articleTypeList",
     *   description="获取内容分类列表",
     *   @OA\Parameter(name="Option",in="query",description="内容选项【TOPIC：话题，ARTICLE：文章；非必填】"),
     *   @OA\Parameter(name="page",in="query",description="当前页"),
     *   @OA\Parameter(name="page_num",in="query",description="每页条数"),
     *   @OA\Response(response="100000", description="success"),
     *   @OA\Response(response="200000", description="fail"),
     * )
     * @param Request $request
     * @return string
     */
    public function lists(Request $request)
    {
        $inputs = $request->only('Option','page','page_num');
        try{
            $inputs['page_limit'] = isset($inputs['page_num']) ? $inputs['page_num'] : 10;
            $result = $this->service ->getArticleTypeList($inputs);
            if(!$result){
                return self::parametersIllegal("没有数据");
            }
            return self::success($result);
        }catch (\Exception $e){
            return self::error($e->getCode(),$e->getMessage());
        }
    }

    /**
     * 获取内容分类详情
     *
     * @OA\Get(
     *   tags={"内容分类"},
     *   path="/api/frontend/article-type/detail/{id}",
     *   operationId="articleTypeDetail",
     *   description="获取内容分类详情",
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
            if(!$result){
                return self::parametersIllegal("没有数据");
            }
            return self::success($result);
        }catch (\Exception $e){
            return self::error($e->getCode(),$e->getMessage());
        }
    }
}
