<?php

namespace Cheney\AdminSystem\Services;

use Cheney\AdminSystem\Models\OperationLog;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class OperationLogService
{
    protected $operationLogModel;

    public function __construct(OperationLog $operationLogModel)
    {
        $this->operationLogModel = $operationLogModel;
    }

    /**
     * 获取操作日志列表
     * 
     * @param array $params 查询参数，支持 username（用户名）、module（模块）、action（操作）、status（状态）、start_date（开始日期）、end_date（结束日期）、per_page（每页数量）
     * @return LengthAwarePaginator 返回分页的操作日志列表
     */
    public function index(array $params = []): LengthAwarePaginator
    {
        $query = $this->operationLogModel->query();

        if (isset($params['username'])) {
            $query->where('username', 'like', '%' . $params['username'] . '%');
        }

        if (isset($params['module'])) {
            $query->where('module', 'like', '%' . $params['module'] . '%');
        }

        if (isset($params['action'])) {
            $query->where('action', 'like', '%' . $params['action'] . '%');
        }

        if (isset($params['status'])) {
            $query->where('status', $params['status']);
        }

        if (isset($params['start_date']) && isset($params['end_date'])) {
            $query->whereBetween('created_at', [$params['start_date'], $params['end_date']]);
        }

        $perPage = $params['per_page'] ?? 15;
        return $query->with('admin')->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * 获取操作日志详情
     * 
     * @param int $id 操作日志ID
     * @return OperationLog 返回操作日志模型实例，包含管理员关联数据
     */
    public function show(int $id)
    {
        return $this->operationLogModel->with('admin')->findOrFail($id);
    }

    /**
     * 删除操作日志
     * 
     * @param int $id 操作日志ID
     * @return bool 删除成功返回true，失败返回false
     */
    public function destroy(int $id): bool
    {
        $log = $this->operationLogModel->findOrFail($id);
        return $log->delete();
    }

    /**
     * 清理指定天数前的操作日志
     * 
     * @param int $days 天数，默认为30天
     * @return int 返回删除的记录数
     */
    public function clear(int $days = 30): int
    {
        return $this->operationLogModel->where('created_at', '<', now()->subDays($days))->delete();
    }

    /**
     * 获取操作日志统计信息
     * 
     * @return array 返回统计信息数组，包含总数、成功数、失败数、模块统计、操作统计
     */
    public function statistics(): array
    {
        $total = $this->operationLogModel->count();
        $success = $this->operationLogModel->success()->count();
        $failed = $this->operationLogModel->failed()->count();
        
        $moduleStats = $this->operationLogModel->selectRaw('module, count(*) as count')
            ->groupBy('module')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();
            
        $actionStats = $this->operationLogModel->selectRaw('action, count(*) as count')
            ->groupBy('action')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();
        
        return [
            'total' => $total,
            'success' => $success,
            'failed' => $failed,
            'module_stats' => $moduleStats,
            'action_stats' => $actionStats,
        ];
    }

    /**
     * 导出操作日志为CSV文件
     * 
     * @param array $params 查询参数，支持 start_date（开始日期）、end_date（结束日期）
     * @return string 返回CSV文件路径
     */
    public function export(array $params = [])
    {
        $query = $this->operationLogModel->query();

        if (isset($params['start_date']) && isset($params['end_date'])) {
            $query->whereBetween('created_at', [$params['start_date'], $params['end_date']]);
        }

        $logs = $query->orderBy('created_at', 'desc')->get();

        $fileName = 'operation_logs_' . date('YmdHis') . '.csv';
        $filePath = storage_path('app/' . $fileName);

        $file = fopen($filePath, 'w');
        
        fputcsv($file, ['ID', '用户名', '模块', '操作', '方法', 'URL', 'IP', '状态', '错误信息', '创建时间']);

        foreach ($logs as $log) {
            fputcsv($file, [
                $log->id,
                $log->username,
                $log->module,
                $log->action,
                $log->method,
                $log->url,
                $log->ip,
                $log->status ? '成功' : '失败',
                $log->error_message,
                $log->created_at,
            ]);
        }

        fclose($file);

        return $filePath;
    }
}
