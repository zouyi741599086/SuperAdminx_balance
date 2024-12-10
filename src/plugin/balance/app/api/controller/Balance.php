<?php
namespace plugin\balance\app\api\controller;

use support\Request;
use support\Response;

use plugin\balance\app\common\logic\BalanceLogic;

/**
 * 用户余额 控制器
 *
 * @author zy <741599086@qq.com>
 * @link https://www.superadminx.com/
 * */
class Balance
{

    // 此控制器是否需要登录
    protected $onLogin = true;
    
    // 不需要登录的方法
    protected $noNeedLogin = [];


    /**
     * 获取用户余额
     * @method get
     * @param Request $request 
     * @return Response
     */
    public function get(Request $request): Response
    {
        $data = BalanceLogic::getUserBalance($request->user['id']);
        return success(data: $data);
    }
}