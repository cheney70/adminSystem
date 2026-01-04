<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2026/1/1
 * Time: 14:25
 */

namespace Cheney\Content;


class Content {
    protected $config;

    public function __construct(array $config){
        $this->config = $config;
    }

    public function say(){
        return $this->config;
    }

}