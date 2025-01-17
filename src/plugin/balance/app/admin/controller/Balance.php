<?php
namespace plugin\balance\app\admin\controller;

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
     * 列表
     * @method get
     * @auth balanceGetList
     * @param Request $request 
     * @return Response
     */
    public function getList(Request $request): Response
    {
        $list = BalanceLogic::getList($request->get());
        return success($list);
    }

    /**
     * 获取余额类型的配置
     * @method get
     * @auth balanceGetList
     * @param Request $request 
     * @return Response
     */
    public function getBalanceType(Request $request)
    {
        return success(config('superadminx.balance_type'));
    }

    /**
     * @log 变更用户余额
     * @method post
     * @auth updateBalance
     * @param Request $request 
     * @return Response
     */
    public function updateBalance(Request $request): Response
    {
        BalanceLogic::updateBalance($request->post());
        return success([], '操作成功');
    }

    /**
     * @log 导出用户余额数据
     * @method get
     * @auth balanceExportData
     * @param Request $request 
     * @return Response
     */
    public function exportData(Request $request): Response
    {
        $data = BalanceLogic::exportData($request->get());
        return success($data);
    }

}