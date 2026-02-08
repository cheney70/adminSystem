<?php

namespace Cheney\AdminSystem\Facades;

use Illuminate\Support\Facades\Facade;

class AdminUser extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'admin.user';
    }
}
