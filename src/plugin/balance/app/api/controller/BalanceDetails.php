<?php
namespace plugin\balance\app\api\controller;

use support\Request;
use support\Response;

use plugin\balance\app\common\logic\BalanceDetailsLogic;

/**
 * 用户余额明细 控制器
 *
 * @author zy <741599086@qq.com>
 * @link https://www.superadminx.com/
 * */
class BalanceDetails
{

    // 此控制器是否需要登录
    protected $onLogin = true;

    // 不需要登录的方法
    protected $noNeedLogin = [];


    /**
     * 列表
     * @method get
     * @auth balanceDetailsGetList
     * @param Request $request 
     * @return Response
     */
    public function getList(Request $request) : Response
    {
        $params            = $request->get();
        $params['user_id'] = $request->user->id;
        if (! isset($params['balance_type'])) {
            return error('参数错误');
        }

        $list = BalanceDetailsLogic::getList($params, true, true);
        return success($list);
    }
}