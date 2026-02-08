<?php

namespace Cheney\AdminSystem\Middleware;

use Closure;
use Illuminate\Http\Request;
use Cheney\AdminSystem\Models\OperationLog;

class OperationLogMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        if (auth('admin')->check()) {
            $admin = auth('admin')->user();
            
            $route = $request->route();
            $action = $route ? $route->getActionName() : '';
            $controllerAction = explode('@', $action);
            $controller = $controllerAction[0] ?? '';
            $method = $controllerAction[1] ?? '';
            
            $module = $this->getModuleName($controller);
            $actionName = $this->getActionName($method);
            
            OperationLog::create([
                'admin_id' => $admin->id,
                'username' => $admin->username,
                'module' => $module,
                'action' => $actionName,
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'params' => $request->except(['password', 'password_confirmation']),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => $response->getStatusCode() < 400 ? 1 : 0,
                'error_message' => $response->getStatusCode() >= 400 ? $response->getContent() : null,
            ]);
        }
        
        return $response;
    }
    
    protected function getModuleName($controller)
    {
        $parts = explode('\\', $controller);
        $className = end($parts);
        return str_replace('Controller', '', $className);
    }
    
    protected function getActionName($method)
    {
        $actionMap = [
            'index' => '列表',
            'store' => '创建',
            'show' => '查看',
            'update' => '更新',
            'destroy' => '删除',
            'assignRoles' => '分配角色',
            'assignPermissions' => '分配权限',
            'resetPassword' => '重置密码',
            'login' => '登录',
            'logout' => '退出',
            'userMenus' => '获取用户菜单',
        ];
        
        return $actionMap[$method] ?? $method;
    }
}
