<?php

namespace Cheney\AdminSystem\Facades;

use Illuminate\Support\Facades\Facade;

class AdminOperationLog extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'admin.operation-log';
    }
}
