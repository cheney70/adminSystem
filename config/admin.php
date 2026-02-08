<?php

return [
    'prefix' => env('ADMIN_PREFIX', 'system'),
    
    'middleware' => [
        'auth' => env('ADMIN_AUTH_MIDDLEWARE', 'jwt'),
        'cors' => env('ADMIN_CORS_MIDDLEWARE', 'cors'),
    ],
    
    'jwt' => [
        'guard' => env('ADMIN_JWT_GUARD', 'api'),
        'ttl' => env('ADMIN_JWT_TTL', 60),
        'refresh_ttl' => env('ADMIN_JWT_REFRESH_TTL', 20160),
    ],
    
    'pagination' => [
        'per_page' => env('ADMIN_PER_PAGE', 15),
    ],
    
    'operation_log' => [
        'enabled' => env('ADMIN_OPERATION_LOG_ENABLED', true),
        'clear_days' => env('ADMIN_OPERATION_LOG_CLEAR_DAYS', 30),
    ],
    
    'upload' => [
        'disk' => env('ADMIN_UPLOAD_DISK', 'public'),
        'path' => env('ADMIN_UPLOAD_PATH', 'uploads/admin'),
        'max_size' => env('ADMIN_UPLOAD_MAX_SIZE', 10240), // KB
        'allowed_extensions' => [
            'jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp',
            'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
            'pdf', 'txt', 'zip', 'rar', '7z',
        ],
    ],
    
    'default_admin' => [
        'username' => env('ADMIN_DEFAULT_USERNAME', 'admin'),
        'password' => env('ADMIN_DEFAULT_PASSWORD', 'admin123'),
        'name' => env('ADMIN_DEFAULT_NAME', '超级管理员'),
        'email' => env('ADMIN_DEFAULT_EMAIL', 'admin@example.com'),
    ],
];
