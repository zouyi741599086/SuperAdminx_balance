<?php

use support\Request;

return [
    'debug'             => getenv('DE_BUG') == 'true' ? true : false,
    'controller_suffix' => '',
    'controller_reuse'  => true,
    'version'           => '1.0.5',

    // 各种余额类型
    'balance_type'      => [
        [
            'field'     => 'money', // 字段名称
            'title'     => '余额', // 字段中文
            'precision' => 2 // 小数点保留位数
        ],
        [
            'field'     => 'integral',
            'title'     => '积分',
            'precision' => 0
        ],
    ]
];
