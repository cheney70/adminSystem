<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2026/1/1
 * Time: 14:39
 */

namespace Cheney\Content\Facade;


use Illuminate\Support\Facades\Facade;

class ContentFacade extends Facade{
    /**
     * Get the registered name of the component.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    protected static function getFacadeAccessor()
    {
        return "content";
    }
}