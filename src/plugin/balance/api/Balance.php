<?php
namespace plugin\balance\api;

use plugin\balance\app\common\logic\BalanceLogic;

/**
 * 用户余额 控制器
 *
 * @author zy <741599086@qq.com>
 * @link https://www.superadminx.com/
 * */
class Balance
{

    /**
     * 变更用户余额
     * @param int $userId 用户id
     * @param float|int $changeValue 变化的值，正数为加，负数为减少
     * @param string $balanceType 余额类型，看config/superadminx.php里面的余额配置
     * @param string $detailType 明细变动类型，看config/superadminx.php里面的余额配置
     * @param string $title 变更的原因
     * @param bool $isNegative 是否允许修改为负数
     * @return void
     */
    public static function change(
        int $userId,
        float|int $changeValue,
        string $balanceType,
        string $detailsType,
        string $title,
        bool $isNegative = false,
    ) : void {
        try {
            if ($changeValue != 0) {
                $params = [
                    'user_id'      => $userId,
                    'change_value' => $changeValue,
                    'balance_type' => $balanceType,
                    'details_type' => $detailsType,
                    'title'        => $title,
                    'isNegative'   => $isNegative,
                ];

                BalanceLogic::updateBalance($params);
            }
        } catch (\Exception $e) {
            abort($e->getMessage());
        }
    }

    /**
     * 获取用户的余额
     * @param int $userId
     * @param string $balanceType 余额类型，看config/superadminx.php里面的余额配置
     * @return mixed
     */
    public static function get(int $userId, ?string $balanceType = null) 
    {
        $balance = BalanceLogic::getUserBalance($userId);
        if ($balanceType) {
            return $balance[$balanceType] ?? 0;
        }
        return $balance;
    }

}