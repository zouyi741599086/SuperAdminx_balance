<?php
namespace plugin\balance\app\api\controller;

use support\Request;
use support\Response;

use plugin\balance\app\common\logic\BalanceWithdrawLogic;

/**
 * 用户余额提现 控制器
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
    // 不需要加密的方法
    protected $noNeedEncrypt = [];

    /**
     * 列表
     * @method get
     * @auth balanceWithdrawGetList
     * @param Request $request 
     * @return Response
     */
    public function getList(Request $request) : Response
    {
        $params            = $request->get();
        $params['user_id'] = $request->user->id;

        $list = BalanceWithdrawLogic::getList($params, true, true);
        return success($list);
    }

    /**
     * @log 新增余额提现
     * @method post
     * @param Request $request 
     * @return Response
     */
    public function create(Request $request) : Response
    {
        $params = $request->post();
        $params['user_id'] = $request->user->id;

        BalanceWithdrawLogic::create($params);
        return success([], '申请成功');
    }

    /**
     * 获取数据
     * @method get
     * @param int $id 
     * @return Response
     */
    public function findData(Request $request, int $id) : Response
    {
        $data = BalanceWithdrawLogic::findData($id, []);
        if (! $data || $data['user_id'] != $request->user->id) {
            return error('参数错误');
        }
        return success($data);
    }

}