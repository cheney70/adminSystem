<?php

namespace Cheney\AdminSystem\Middleware;

use Closure;
use Illuminate\Http\Request;
use Cheney\AdminSystem\Traits\ApiResponseTrait;

class JwtMiddleware
{
    use ApiResponseTrait;

    public function handle(Request $request, Closure $next)
    {
        try {
            $admin = auth('admin')->user();
            
            if (!$admin) {
                return $this->unauthorized();
            }
            
            if ($admin->status != 1) {
                return $this->forbidden('账号已被禁用');
            }
            
            return $next($request);
        } catch (\Exception $e) {
            return $this->unauthorized('Token无效或已过期');
        }
    }
}
