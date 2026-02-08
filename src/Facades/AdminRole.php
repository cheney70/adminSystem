<?php

namespace Cheney\AdminSystem\Facades;

use Illuminate\Support\Facades\Facade;

class AdminRole extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'admin.role';
    }
}
