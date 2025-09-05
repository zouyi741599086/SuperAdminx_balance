<?php
/**
 * This file is part of SuperAdminx.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author zy <741599086@qq.com>
 * @link https://www.superadminx.com/
 */

return [
    // 各种余额类型
    'balance_type' => [
        [
            'field'                => 'money', // 字段名称
            'title'                => '余额', // 字段中文
            'precision'            => 2, // 小数点保留位数
            'details'              => true, // 是否有明细记录
            //'submeter_start_month' => '2025-08', // 明细是否有使用分表，分表开始的月份，空代表没分表
            'details_type'         => [ // 明细类型
                'money_balance_withdraw' => '提现',
                'money_order_create'     => '订单支付',
                'money_order_return'     => '订单退款',
                'money_other'            => '其它',
            ],
        ],
        [
            'field'        => 'integral',
            'title'        => '积分',
            'precision'    => 0,
            'details'      => true,
            //'submeter_start_month' => '2025-08',
            'details_type' => [
                'integral_create_order' => '兑换订单',
                'integral_order_dikou'  => '订单支付',
                'integral_order_close'  => '订单关闭',
            ],
        ],
    ],
];
