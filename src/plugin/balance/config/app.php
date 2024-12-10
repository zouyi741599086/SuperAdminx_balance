<?php

use support\Request;

return [
    'debug' => getenv('DE_BUG') == 'true' ? true : false,
    'controller_suffix' => '',
    'controller_reuse' => true,
    'version' => '1.0.0',

    // 各种余额类型
    'balance_type' => [
        'money' => '余额',
        'integral' => '积分',
    ]
];
