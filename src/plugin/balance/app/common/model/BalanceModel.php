<?php
namespace plugin\balance\app\common\model;

use app\common\model\BaseModel;

/**
 * 用户余额 模型
 *
 * @author zy <741599086@qq.com>
 * @link https://www.superadminx.com/
 * */
class BalanceModel extends BaseModel
{

    // 表名
    protected $name = 'balance';

    // 自动时间戳
    protected $autoWriteTimestamp = true;

    // 字段类型转换
    protected $type = [
    ];

    // 包含附件的字段，''代表直接等于附件路劲，'array'代表数组中包含附件路劲，'editor'代表富文本中包含附件路劲
    protected $file = [
    ];


    // 所属用户 搜索器
    public function searchUserIdAttr($query, $value, $data)
    {
        ($value != null) && $query->where('user_id', '=', $value);
    }

    // 更新时间 搜索器
    public function searchUpdateTimeAttr($query, $value, $data)
    {
        ($value && is_array($value)) && $query->where('update_time', 'between', ["{$value[0]} 00:00:00", "{$value[1]} 23:59:59"]);
    }

    // 所属用户 关联模型
    public function User()
    {
        return $this->belongsTo(\app\common\model\UserModel::class);
    }

}