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
     * @param string $balanceType 余额类型，看app配置
     * @param float|int $changeValue 变化的值，正数为加，负数为减少
     * @param string $title 变更的原因
     * @return void
     */
    public static function change(int $userId, string $balanceType, float|int $changeValue, string $title) : void
    {
        try {
            $configBalanceType = config('plugin.balance.app.balance_type');
            if (! isset($configBalanceType[$balanceType])) {
                throw new \Exception("变更的余额类型不存在");
            }

            if ($changeValue == 0) {
                throw new \Exception("变更余额的值不能等于0");
            }

            BalanceLogic::updateBalance([
                'user_id'      => $userId,
                'type'         => $changeValue > 0 ? 1 : 2,
                'change_value' => $changeValue > 0 ? $changeValue : $changeValue * -1,
                'balance_type' => $balanceType,
                'title'        => $title,
            ]);
        } catch (\Exception $e) {
            abort($e->getMessage());
        }
    }

    /**
     * 获取用户的余额
     * @param int $userId
     * @return mixed
     */
    public static function get(int $userId) 
    {
        return BalanceLogic::getUserBalance($userId);
    }

}