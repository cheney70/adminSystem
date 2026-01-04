<?php

namespace Cheney\Content\Http\Constants;

/**
 * 通用状态配置
 */
class CommonStatusConstant
{
    /**
     * 状态-启用
     */
    const CONSTANT_STATUS_COMMON_ENABLE = 'ENABLE';

    /**
     * 状态-禁用
     */
    const CONSTANT_STATUS_COMMON_DISABLE = 'DISABLE';

    const CONSTANT_STATUS_COMMON_LIST = [
        self::CONSTANT_STATUS_COMMON_ENABLE  => '启用',
        self::CONSTANT_STATUS_COMMON_DISABLE => '禁用'
    ];

    const CONSTANT_STATUS_COMMON_SWITCH = [
        self::CONSTANT_STATUS_COMMON_ENABLE  => true,
        self::CONSTANT_STATUS_COMMON_DISABLE => false
    ];
}
