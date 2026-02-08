<?php

namespace Cheney\AdminSystem\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    protected $middleware = [
        \Cheney\AdminSystem\Middleware\HandleCors::class,
    ];

    protected $middlewareGroups = [
        'web' => [],
        'api' => [
            'cors',
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    protected $routeMiddleware = [
        'cors' => \Cheney\AdminSystem\Middleware\HandleCors::class,
        'jwt' => \Cheney\AdminSystem\Middleware\JwtMiddleware::class,
        'permission' => \Cheney\AdminSystem\Middleware\PermissionMiddleware::class,
        'operation.log' => \Cheney\AdminSystem\Middleware\OperationLogMiddleware::class,
    ];
}
