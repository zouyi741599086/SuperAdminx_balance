<?php
namespace plugin\balance\app\common\logic;

use plugin\balance\app\common\model\BalanceModel;
use plugin\balance\app\common\validate\BalanceDetailsValidate;
use plugin\balance\app\common\logic\BalanceDetailsLogic;
use think\facade\Db;

/**
 * 用户余额 逻辑层
 *
 * @author zy <741599086@qq.com>
 * @link https://www.superadminx.com/
 * */
class BalanceLogic
{

    /**
     * 列表
     * @param array $params get参数
     * @param bool $page 是否需要翻页
     * */
    public static function getList(array $params = [], bool $page = true)
    {
        // 排序
        $orderBy = "id desc";
        if (isset($params['orderBy']) && $params['orderBy']) {
            $orderBy = "{$params['orderBy']},{$orderBy}";
        }

        $list = BalanceModel::withSearch(['user_id', 'update_time'], $params)
            ->with(['User' => function ($query)
            {
                $query->field('id,img,name,tel');
            }])
            ->order($orderBy);

        return $page ? $list->paginate($params['pageSize'] ?? 20) : $list->select();
    }

    /**
     * 变更用户余额
     * @param array $params
     */
    public static function updateBalance(array $params)
    {
        Db::startTrans();
        try {
            validate(BalanceDetailsValidate::class)->check($params);

            if ($params['change_value'] == 0) {
                throw new \Exception("变更余额的值不能等于0");
            }
            $data = self::getUserBalance($params['user_id']);

            // 增加余额
            if ($params['type'] == 1) {
                $data->inc($params['balance_type'], $params['change_value'])->save();
            }
            // 减少余额
            if ($params['type'] == 2) {
                if ($params['change_value'] > $data[$params['balance_type']]) {
                    $tmp = self::findBalanceType($params['balance_type']);
                    throw new \Exception("用户{$tmp['title']}不足");
                }
                $data->dec($params['balance_type'], $params['change_value'])->save();
            }

            // 增加明细
            BalanceDetailsLogic::create($params);

            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            abort($e->getMessage());
        }
    }

    /**
     * 查找余额类型
     * @param string $balanceType
     * @return mixed
     */
    public static function findBalanceType(string $balanceType)
    {
        $balanceTypeList = config('superadminx.balance_type');
        $tmp             = null;
        foreach ($balanceTypeList as $v) {
            if ($v['field'] == $balanceType) {
                $tmp = $v;
                break;
            }
        }
        if (! $tmp) {
            abort('余额类型错误');
        }
        return $tmp;
    }

    /**
     * 获取用户的余额
     * @param int $userId
     * @return mixed
     */
    public static function getUserBalance(int $userId)
    {
        $data = BalanceModel::where('user_id', $userId)->hidden(['create_time', 'update_time'])->find();
        // 不存在则新增用户余额
        if (! $data) {
            BalanceModel::create([
                'user_id' => $userId
            ]);
            $data = BalanceModel::where('user_id', $userId)->hidden(['create_time', 'update_time'])->find();
        }
        return $data;
    }

    /**
     * 导出数据
     * @param array $params get参数，用于导出数据的控制
     */
    public static function exportData(array $params)
    {
        try {
            $balanceTypeList = config('superadminx.balance_type');
            // 表格头
            $header = array_merge(
                ['用户'],
                array_column($balanceTypeList, 'title'),
                ['变更时间']
            );
            $list    = self::getList($params, false);
            $tmpList = [];
            foreach ($list as $v) {
                // 导出的数据
                $tmp   = [];
                $tmp[] = "{$v['User']['name']}/{$v['User']['tel']}";

                // 防止余额类型的顺序跟数据库的对不上
                foreach ($balanceTypeList as $val) {
                    $tmp[] = $v[$val['field']] ?? 0;
                }

                $tmp[]     = $v['update_time'] ?? '';
                $tmpList[] = $tmp;
            }
            // 开始生成表格导出
            $config   = [
                'path' => public_path() . '/tmp_file',
            ];
            $fileName = "用户余额.xlsx";
            $excel    = new \Vtiful\Kernel\Excel($config);
            $filePath = $excel->fileName(rand(1, 10000) . time() . '.xlsx')
                ->header($header)
                ->data($tmpList)
                ->output();
            $excel->close();

            return [
                'filePath' => str_replace(public_path(), '', $filePath),
                'fileName' => $fileName
            ];
        } catch (\Exception $e) {
            abort($e->getMessage());
        }
    }

}