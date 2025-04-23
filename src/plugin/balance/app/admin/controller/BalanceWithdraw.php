<?php
namespace plugin\balance\app\admin\controller;

use support\Request;
use support\Response;

use plugin\balance\app\common\logic\BalanceWithdrawLogic;

/**
 * 余额提现 控制器
 *
 * @author zy <741599086@qq.com>
 * @link https://www.superadminx.com/
 * */
class BalanceWithdraw
{

    // 此控制器是否需要登录
    protected $onLogin = true;

    // 不需要登录的方法
    protected $noNeedLogin = [];


    /**
     * 列表
     * @method get
     * @auth balanceWithdrawGetList
     * @param Request $request 
     * @return Response
     */
    public function getList(Request $request) : Response
    {
        $list = BalanceWithdrawLogic::getList($request->get());
        return success($list);
    }


    /**
     * 获取数据
     * @method get
     * @param int $id 
     * @return Response
     */
    public function findData(int $id) : Response
    {
        $data = BalanceWithdrawLogic::findData($id);
        return success($data);
    }

    /**
     * @log 修改余额提现状态
     * @method post
     * @auth balanceWithdrawUpdateStatus
     * @param int|array $id 数据id
     * @param int $status 数据状态 
     * @return Response
     */
    public function updateStatus(Request $request, array $id, int $status, string $reason = '') : Response
    {
        BalanceWithdrawLogic::updateStatus($id, $status, $reason);
        return success();
    }

    /**
     * @log 导出余额提现数据
     * @method get
     * @auth balanceWithdrawExportData
     * @param Request $request 
     * @return Response
     */
    public function exportData(Request $request) : Response
    {
        $data = BalanceWithdrawLogic::exportData($request->get());
        return success($data);
    }


}