<?php
namespace plugin\balance\app\common\logic;

use plugin\balance\app\common\model\BalanceDetailsModel;
use plugin\balance\app\common\model\BalanceModel;
use plugin\balance\app\common\validate\BalanceDetailsValidate;
use think\facade\Db;

/**
 * 用户余额明细 逻辑层
 *
 * @author zy <741599086@qq.com>
 * @link https://www.superadminx.com/
 * */
class BalanceDetailsLogic
{

    /**
     * 列表
     * @param array $params get参数
     * @param bool $page 是否需要翻页
     * @param bool $filter 是否是用户端在调用
     * */
    public static function getList(array $params = [], bool $page = true, bool $filter)
    {
        // 排序
        $orderBy = "id desc";
        if (isset($params['orderBy']) && $params['orderBy']) {
            $orderBy = "{$params['orderBy']},{$orderBy}";
        }

        $with = ['User' => function ($query)
        {
            $query->field('id,img,name,tel');
        }];
        // 如果是用户查询自己的，则不需要关联
        if ($filter) {
            $with = [];
        }

        $list = BalanceDetailsModel::withSearch(['user_id', 'balance_type', 'title', 'type', 'create_time'], $params)
            ->with($with)
            ->order($orderBy);

        return $page ? $list->paginate($params['pageSize'] ?? 20) : $list->select();
    }

    /**
     * 新增
     * @param array $params
     */
    public static function create(array $params)
    {
        Db::startTrans();
        try {
            validate(BalanceDetailsValidate::class)->check($params);

            // 变化后的余额
            $params['change_balance'] = BalanceModel::where('user_id', $params['user_id'])
                ->value($params['balance_type']);

            BalanceDetailsModel::create($params);

            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            abort($e->getMessage());
        }
    }

    /**
     * 导出数据
     * @param array $params get参数，用于导出数据的控制
     */
    public static function exportData(array $params)
    {
        try {
            $balanceType = config('plugin.balance.app.balance_type');
            // 表格头
            $header = ['用户', '余额类型', '标题', '类型', '变更值', '变更后余额', '变化时间'];

            $list    = self::getList($params, false);
            $tmpList = [];
            foreach ($list as $v) {
                // 导出的数据
                $tmpList[] = [
                    "{$v['User']['name']}/{$v['User']['tel']}",
                    $balanceType[$v['balance_type']] ?? '',
                    $v['title'] ?? '',
                    $v['type'] == 1 ? '增加' : '减少',
                    $v['change_value'] ?? '',
                    $v['change_balance'] ?? '',
                    $v['create_time'] ?? '',
                ];
            }
            // 开始生成表格导出
            $config   = [
                'path' => public_path() . '/tmp_file',
            ];
            $fileName = "用户余额明细.xlsx";
            $excel    = new \Vtiful\Kernel\Excel($config);
            $filePath = $excel->fileName(rand(1, 10000) . time() . '.xlsx')
                ->header($header)
                ->data($tmpList)
                ->output();
            $excel->close();

            return [
                'filePath' => $filePath,
                'fileName' => $fileName
            ];
        } catch (\Exception $e) {
            abort($e->getMessage());
        }
    }

}