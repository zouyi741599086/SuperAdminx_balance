<?php
namespace plugin\balance\app\common\logic;

use plugin\balance\app\common\model\BalanceWithdrawModel;
use plugin\balance\app\common\logic\BalanceLogic;
use plugin\balance\app\common\validate\BalanceWithdrawValidate;
use plugin\balance\api\Balance;
use plugin\admin\app\common\logic\ConfigLogic;
use think\facade\Db;

/**
 * 余额提现 逻辑层
 *
 * @author zy <741599086@qq.com>
 * @link https://www.superadminx.com/
 * */
class BalanceWithdrawLogic
{
    /**
     * 列表
     * @param array $params get参数
     * @param bool $page 是否需要翻页，不翻页返回模型
     * @param bool $filter 是否是用户端在调用
     * */
    public static function getList(array $params = [], bool $page = true, bool $filter = false)
    {
        // 排序
        $orderBy = "id desc";
        if (isset($params['orderBy']) && $params['orderBy']) {
            $orderBy = "{$params['orderBy']},{$orderBy}";
        }

        // 用户端在调用，则不需要关联用户信息
        $with = ['User' => function ($query)
        {
            $query->field('id,name,tel,img');
        }];
        if ($filter) {
            $with = [];
        }

        $list = BalanceWithdrawModel::withSearch(['user_id', 'orderno', 'status', 'bank_name', 'bank_title', 'bank_number', 'create_time', 'audit_time', 'reason'], $params)
            ->with($with)
            ->order($orderBy);

        return $page ? $list->paginate($params['pageSize'] ?? 20) : $list;
    }

    /**
     * 新增，申请提现
     * @param array $params
     * @param string $balanceType 提现的是balance表中哪个字段
     */
    public static function create(array $params, string $balanceType = 'money')
    {
        Db::startTrans();
        try {
            validate(BalanceWithdrawValidate::class)->check($params);

            // 提现的相关配置
            $balanceWithdrawConfig = ConfigLogic::getConfig('balance_withdraw_config');

            if ($params['money'] < $balanceWithdrawConfig->min_money) {
                throw new \Exception('提现金额不能低于' . $balanceWithdrawConfig->min_money);
            }

            // 减少余额
            Balance::change(
                $params['user_id'],
                -$params['money'],
                $params['balance_type'],
                'money_balance_withdraw',
                '提现申请'
            );

            $params['shouxufei'] = d2($params['money'] * $balanceWithdrawConfig->shouxufei_bili / 100);
            $params['on_money']  = $params['money'] - $params['shouxufei'];
            $params['orderno']   = get_order_no('TX');

            BalanceWithdrawModel::create($params);
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            abort($e->getMessage());
        }
    }

    /**
     * 获取数据
     * @param int $id 数据id
     * @param array $with 关联模型
     */
    public static function findData(int $id, array $with = ['User'])
    {
        return BalanceWithdrawModel::with($with)->find($id);
    }

    /**
     * 获取最后一次提现的详情
     * @param int $userId 用户id
     */
    public static function getLastInfo(int $userId)
    {
        return BalanceWithdrawModel::where('user_id', $userId)->order('id', 'desc')->find();
    }

    /**
     * 更新状态
     * @param int|array $id
     * @param int $status
     * @param string $reason
     */
    public static function updateStatus(int|array $id, int $status, string $reason = '')
    {
        Db::startTrans();
        try {
            //审核通过
            if ($status == 4) {
                BalanceWithdrawModel::where('id', 'in', $id)->where('status', 2)->update([
                    'status'     => $status,
                    'audit_time' => date('Y-m-d H:i:s')
                ]);
            }

            //审核拒绝
            if ($status == 6) {
                $id = BalanceWithdrawModel::where('id', 'in', $id)
                    ->where('status', 2)
                    ->column('id');

                //更改状态
                BalanceWithdrawModel::where('id', 'in', $id)
                    ->update([
                        'status'     => $status,
                        'audit_time' => date('Y-m-d H:i:s'),
                        'reason'     => $reason,
                    ]);
                //给用户把钱加回去
                foreach ($id as $tmpId) {
                    $data = BalanceWithdrawModel::where('id', $tmpId)->find();
                    //给用户加钱
                    BalanceLogic::updateBalance([
                        'user_id'      => $data['user_id'],
                        'change_value' => $data['money'],
                        'balance_type' => $data['balance_type'],
                        'details_type' => 'money_balance_withdraw',
                        'title'        => "提现审核拒绝[{$data['orderno']}]",
                    ]);
                }
            }

            //已打款
            if ($status == 7) {
                BalanceWithdrawModel::where('id', 'in', $id)
                    ->where('status', 4)
                    ->update([
                        'status'   => $status,
                        'pay_time' => date('Y-m-d H:i:s'),
                    ]);
            }

            //打款成功
            if ($status == 8) {
                BalanceWithdrawModel::where('id', 'in', $id)
                    ->where('status', 4)
                    ->update([
                        'status'   => $status,
                        'pay_time' => date('Y-m-d H:i:s'), // 如果是后台直接操作打款成功，则需要加上打款时间
                    ]);
            }

            //打款失败
            if ($status == 10) {
                $id = BalanceWithdrawModel::where('id', 'in', $id)
                    ->where('status', 4)
                    ->column('id');
                //更改状态
                BalanceWithdrawModel::where('id', 'in', $id)
                    ->update([
                        'status'   => $status,
                        'pay_time' => date('Y-m-d H:i:s'),
                        'reason'   => $reason,
                    ]);
                //给用户把钱加回去
                foreach ($id as $tmpId) {
                    $data = BalanceWithdrawModel::where('id', $tmpId)->find();
                    //给用户加钱
                    BalanceLogic::updateBalance([
                        'user_id'      => $data['user_id'],
                        'change_value' => $data['money'],
                        'balance_type' => $data['balance_type'],
                        'details_type' => 'money_balance_withdraw',
                        'title'        => "提现打款失败[{$data['orderno']}]",
                    ]);
                }
            }
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            abort($e);
        }
    }


    /**
     * 导出数据
     * @param array $params get参数，用于导出数据的控制
     */
    public static function exportData(array $params)
    {
        try {
            $list    = self::getList($params, false)->cursor();
            $tmpList = [];
            foreach ($list as $v) {

                switch ($v['status']) {
                    case 2:
                        $v['status'] = '待审核';
                        break;
                    case 4:
                        $v['status'] = '审核通过待打款';
                        break;
                    case 6:
                        $v['status'] = '审核拒绝';
                        break;
                    case 7:
                        $v['status'] = '打款中';
                        break;
                    case 8:
                        $v['status'] = '打款成功';
                        break;
                    case 10:
                        $v['status'] = '打款失败';
                        break;
                }

                // 导出的数据
                $tmpList[] = [
                    "{$v->User->name}/{$v->User->tel}",
                    $v->orderno ?? '',
                    $v->status ?? '',
                    $v->money ?? '',
                    $v->shouxufei ?? '',
                    $v->on_money ?? '',
                    $v->bank_name ?? '',
                    $v->bank_title ?? '',
                    $v->bank_number ?? '',
                    $v->create_time ?? '',
                    $v->audit_time ?? '',
                    $v->pay_time ?? '',
                    $v->reason ?? '',
                ];
            }

            // 表格头
            $header = ['用户', '单号', '状态', '提现金额', '手续费', '到账金额', '姓名', '银行', '银行卡号', '申请时间', '审核时间', '打款时间', '失败原因'];
            return [
                'filePath' => export($header, $tmpList),
                'fileName' => "余额提现.xlsx"
            ];
        } catch (\Exception $e) {
            abort($e->getMessage());
        }
    }

}