<?php
namespace Cheney\Content\Http\Services;

use App\Exceptions\FileNotExistException;
use Cheney\Content\Http\Constants\CommonStatusConstant;
use  Cheney\Content\Http\ArticleTypes;
use Carbon\Carbon;

/**
 * Created by PhpStorm.
 * User: codeanti
 * Date: 2020-1-4
 * Time: 下午3:08
 */
class ArticleTypeService
{
    /**
     * @return void
     */
    public function getArticleTypeList($params){
        $model = ArticleTypes::query();
        $model ->where('status',CommonStatusConstant::CONSTANT_STATUS_COMMON_ENABLE);
        if (isset($params['Id']) && !empty($params['Id'])){
            $model ->where('id',$params['Id']);
        }
        $orderBy   = isset($params['orderBy']) ? $params['orderBy'] : 'id';
        $orderSort = isset($params['byAsc']) ? 'ASC' : 'DESC';
        $model->orderBy($orderBy,$orderSort);
        if(isset($params['groupBy']) && $params['groupBy']){
            $model->groupBy($params['groupBy']);
        }
        if(! $model->exists()){
            return false;
        }
        if(isset($params['page_num']) && $params['page_num']){
            $page    = isset($params['page']) ? $params['page'] : 1;
            $result = $model->paginate($params['page_num'],['*'],'page',$page);
        }else{
            $result = $model->get();
        }
        return $result;
    }

}
