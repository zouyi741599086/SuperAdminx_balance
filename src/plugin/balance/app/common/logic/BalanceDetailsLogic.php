<?php
namespace plugin\balance\app\common\logic;

use plugin\balance\app\common\model\BalanceModel;
use plugin\balance\app\common\validate\BalanceDetailsValidate;

/**
 * 用户余额明细 逻辑层
 *
 * @author zy <741599086@qq.com>
 * @link https://www.superadminx.com/
 * */
class BalanceDetailsLogic
{
    /**
     * 根据余额类型获取对应的余额明细的模型
     * @param string $balanceType 余额类型
     * @param string $submeterMonth 分表的月份，如2025-01
     */
    public static function getModel(string $balanceType, ?string $submeterMonth = null)
    {
        if (! $balanceType) {
            abort('余额模型类型错误');
        }
        // 使用空格替换字符串中的下划线  
        $balanceTypeModel = str_replace('_', ' ', $balanceType);
        // 使用ucwords函数将字符串中的每个单词首字母转换为大写  
        $balanceTypeModel = ucwords($balanceTypeModel);
        // 将空格替换为空，实现驼峰命名  
        $balanceTypeModel = str_replace(' ', '', $balanceTypeModel);

        $path  = "\\plugin\\balance\\app\\common\\model\\Balance{$balanceTypeModel}DetailsModel";
        $model = new $path();

        // 判断是否有使用分表
        $balanceTypeList = config('plugin.balance.superadminx.balance_type', 'array');
        foreach ($balanceTypeList as $key => $value) {
            if ($value['field'] == $balanceType && isset($value['submeter_start_month']) && $value['submeter_start_month']) {
                $date   = \DateTime::createFromFormat('Y-m', $submeterMonth ?: date('Y-m'));
                $suffix = '_' . $date->format('y') . $date->format('m');
                break;
            }
        }

        return $model::suffix($suffix ?? '');
    }

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

        $with = ['User' => function ($query)
        {
            $query->field('id,img,name,tel');
        }];
        // 如果是用户查询自己的，则不需要关联
        if ($filter) {
            $with = [];
        }

        $list = (self::getModel($params['balance_type'], $params['submeter_month'] ?? null))
            ->withSearch(['user_id', 'title', 'details_type', 'create_time'], $params, true)
            ->with($with)
            ->order($orderBy);

        return $page ? $list->paginate($params['pageSize'] ?? 20) : $list;
    }

    /**
     * 新增
     * @param array $params
     */
    public static function create(array $params)
    {
        try {
            think_validate(BalanceDetailsValidate::class)->check($params);

            $model = self::getModel($params['balance_type']);
            $model->save([
                ...$params,
                ...[
                    'change_balance' => BalanceModel::where('user_id', $params['user_id'])->value($params['balance_type'])
                ]
            ]);
        } catch (\Exception $e) {
            var_dump($e);
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
            $balanceTypeList  = config('plugin.balance.superadminx.balance_type');
            $balanceTypeTitle = '';
            $detailsTypeList  = [];
            foreach ($balanceTypeList as $v) {
                if ($v['field'] == $params['balance_type']) {
                    $balanceTypeTitle = $v['title'];
                    $detailsTypeList  = $v['details_type'];
                    break;
                }
            }

            if (self::getList($params, false, true)->count() > 100 * 10000) {
                abort('每次最多导出100万条数据，请筛选条件减少数据~');
            }

            $tmpList = [];
            $list    = self::getList($params, false, true)->cursor();
            foreach ($list as $v) {
                // 导出的数据
                $tmpList[] = [
                    $v->user_id,
                    $v->title ?? '',
                    $v->details_type ? ($detailsTypeList[$v->details_type] ?? '--') : '--',
                    $v->change_value,
                    $v->change_balance ?? '',
                    $v->create_time ?? '',
                ];
            }

            // 表格头
            $header = ['用户ID', '标题', '明细类型', '变更值', '变更后余额', '变化时间'];
            return [
                'filePath' => export($header, $tmpList),
                'fileName' => "{$balanceTypeTitle}明细.xlsx"
            ];
        } catch (\Exception $e) {
            abort($e->getMessage());
        }
    }

}