<?php
namespace plugin\balance\app\common\model;

use app\common\model\BaseModel;
use plugin\user\app\common\model\UserModel;

/**
 * 用户余额明细 模型
 *
 * @author zy <741599086@qq.com>
 * @link https://www.superadminx.com/
 * */
class BalanceIntegralDetailsModel extends BaseModel
{

    // 表名
    protected $name = 'balance_integral_details';

    // 自动时间戳
    protected $updateTime = false;

    // 字段类型转换
    protected $type = [
        'change_value' => 'float',
        'change_balance' => 'float',
    ];

    // 包含附件的字段，''代表直接等于附件路劲，'array'代表数组中包含附件路劲，'editor'代表富文本中包含附件路劲
    protected $file = [
    ];

    // 用户 搜索器
    public function searchUserIdAttr($query, $value, $data)
    {
        $query->where('user_id', '=', $value);
    }

    // 明细类型 搜索器
    public function searchDetailsTypeAttr($query, $value, $data)
    {
        $query->where('details_type', '=', $value);
    }

    // 标题 搜索器
    public function searchTitleAttr($query, $value, $data)
    {
        $query->where('title', 'like', "%{$value}%");
    }

    // 变化时间 搜索器
    public function searchCreateTimeAttr($query, $value, $data)
    {
        $query->where('create_time', 'between', ["{$value[0]} 00:00:00", "{$value[1]} 23:59:59"]);
    }

    // 变化时间小于 搜索器
    public function searchCreateTimeLtAttr($query, $value, $data)
    {
        $query->where('create_time', '<', $value);
    }

    // 所属用户 关联模型
    public function User()
    {
        return $this->belongsTo(UserModel::class);
    }

}