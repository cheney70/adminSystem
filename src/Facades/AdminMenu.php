<?php

namespace Cheney\AdminSystem\Facades;

use Illuminate\Support\Facades\Facade;

class AdminMenu extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'admin.menu';
    }
}
