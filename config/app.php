<?php

return [  
    'providers' => [        
        Tymon\JWTAuth\Providers\LaravelServiceProvider::class,
        L5Swagger\L5SwaggerServiceProvider::class,
    ],

    'aliases' => [       
        'JWTAuth' => Tymon\JWTAuth\Facades\JWTAuth::class,
        'JWTFactory' => Tymon\JWTAuth\Facades\JWTFactory::class,
    ],
];