import { http } from '@/common/axios.js'
import { config } from '@/common/config';

/**
 * 用户余额 API
 *
 * @author zy <741599086@qq.com>
 * @link https://www.superadminx.com/
 * */
export const balanceApi = {
    //列表
    getList: (params = {}) => {
        return http.get('/app/balance/admin/Balance/getList',params);
    },
    //获取余额类型的配置
    getBalanceType: (params = {}) => {
        return http.get('/app/balance/admin/Balance/getBalanceType',params);
    },
    //变更用户的余额
    updateBalance: (params = {}) => {
        return http.post('/app/balance/admin/Balance/updateBalance',params);
    },
    //导出数据
    exportData: (params = {}) => {
        return http.get('/app/balance/admin/Balance/exportData',params);
    },
        
}