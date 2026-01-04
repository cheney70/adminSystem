<?php
namespace Cheney\Content\Http\Services;

use App\Exceptions\FileNotExistException;
use Cheney\Content\Http\Models\Articles;
use Carbon\Carbon;

/**
 * Created by PhpStorm.
 * User: codeanti
 * Date: 2020-1-4
 * Time: 下午3:08
 */
class ArticleService
{
    /**
     * @return void
     */
    public function getArticleList($params){
        $model = Articles::query();
        if (isset($params['TypeId']) && !empty($params['TypeId'])){
            $model ->where('type_id',$params['TypeId']);
        }
        if (isset($params['IsTop']) && !empty($params['IsTop'])){
            $model ->where('is_top',$params['IsTop']);
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

    /**
     * @param Int $id
     * @return mixed
     */
    public function getById($id,$with=null,$col='*') {
        $model = Articles::query();
        if(!is_null($with)){
            $result = $model::with($with)->selectRaw($col)->findOrFail($id);
        }else{
            $result = $model::selectRaw($col)->findOrFail($id);
        }
        return $result;
    }

}
