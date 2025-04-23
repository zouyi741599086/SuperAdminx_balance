<?php
namespace plugin\balance\app\common\model;

use app\common\model\BaseModel;

/**
 * 用户余额明细 模型
 *
 * @author zy <741599086@qq.com>
 * @link https://www.superadminx.com/
 * */
class BalanceDetailsModel extends BaseModel
{

    // 表名
    protected $name = 'balance_details';

    // 自动时间戳
    protected $autoWriteTimestamp = true;

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
        ($value != null) && $query->where('user_id', '=', $value);
    }

    // 余额类型 搜索器
    public function searchBalanceTypeAttr($query, $value, $data)
    {
        ($value != null) && $query->where('balance_type', '=', $value);
    }

    // 标题 搜索器
    public function searchTitleAttr($query, $value, $data)
    {
        ($value != null) && $query->where('title', 'like', "%{$value}%");
    }

    // 类型 搜索器
    public function searchTypeAttr($query, $value, $data)
    {
        ($value != null) && $query->where('type', '=', $value);
    }

    // 变化时间 搜索器
    public function searchCreateTimeAttr($query, $value, $data)
    {
        ($value && is_array($value)) && $query->where('create_time', 'between', ["{$value[0]} 00:00:00", "{$value[1]} 23:59:59"]);
    }

    // 所属用户 关联模型
    public function User()
    {
        return $this->belongsTo(\app\common\model\UserModel::class);
    }

}