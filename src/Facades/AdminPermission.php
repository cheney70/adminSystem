<?php

namespace Cheney\AdminSystem\Facades;

use Illuminate\Support\Facades\Facade;

class AdminPermission extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'admin.permission';
    }
}
