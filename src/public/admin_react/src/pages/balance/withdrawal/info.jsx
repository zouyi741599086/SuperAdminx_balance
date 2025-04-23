import { useRef, useState } from 'react';
import {
    ModalForm,
    ProForm,
    ProFormDigit
} from '@ant-design/pro-components';
import { balanceWithdrawApi } from '@/api/balanceWithdraw';
import { App, Button, Input, Descriptions, Typography, Space } from 'antd';
import { useUpdateEffect } from 'ahooks';
import {
    ZoomInOutlined,
} from '@ant-design/icons';
import { authCheck } from '@/common/function';

/**
 * @param fun tableReload 刷新表格
 */
export default ({ infoId, setInfoId, ...props }) => {
    const formRef = useRef();
    const { message } = App.useApp();

    const [open, setOpen] = useState(false);
    const [data, setData] = useState({});

    useUpdateEffect(() => {
        if (infoId > 0) {
            setOpen(true);
            balanceWithdrawApi.findData({
                id: infoId
            }).then(res => {
                if (res.code === 1) {
                    setData(res.data);
                } else {
                    message.error(res.message)
                }
            })
        }
    }, [infoId])


    return (
        <ModalForm
            name="tixianinfo"
            formRef={formRef}
            open={open}
            onOpenChange={(_boolean) => {
                setOpen(_boolean);
                //关闭的时候干掉infoId，不然无法重复修改同一条数据
                if (_boolean === false) {
                    setInfoId(0);
                }
            }}
            footer={null}
            title="提现详情"
            width={460}
        >
            <Descriptions size="small" column={1} bordered={true}>
                <Descriptions.Item label="用户">{data?.User?.name}/{data?.User?.tel}</Descriptions.Item>
                <Descriptions.Item label="订单编号">{data?.orderno}</Descriptions.Item>
                <Descriptions.Item label="状态">
                    {data?.status === 2 ? '待审核' : ''}
                    {data?.status === 4 ? '审核通过待打款' : ''}
                    {data?.status === 6 ? '审核拒绝' : ''}
                    {data?.status === 8 ? '已打款' : ''}
                    {data?.status === 10 ? '打款失败' : ''}
                </Descriptions.Item>
                <Descriptions.Item label="提现金额">
                    <Typography.Text type='danger'>￥{data?.money}</Typography.Text>
                </Descriptions.Item>
                <Descriptions.Item label="手续费">
                    <Typography.Text type='warning'>￥{data?.shouxufei}</Typography.Text>
                </Descriptions.Item>
                <Descriptions.Item label="真实到账">
                    <Typography.Text type='success'>￥{data?.on_money}</Typography.Text>
                </Descriptions.Item>
                <Descriptions.Item label="提现账号">
                    {data?.bank_title}/{data?.bank_name}<br />
                    {data?.bank_number}
                </Descriptions.Item>
                <Descriptions.Item label="申请时间">{data?.create_time}</Descriptions.Item>
                {data?.audit_time ? <>
                    <Descriptions.Item label="审核时间">{data?.audit_time}</Descriptions.Item>
                </> : <></>}
                {data?.pay_time ? <>
                    <Descriptions.Item label="打款时间">{data?.pay_time}</Descriptions.Item>
                </> : <></>}
                {data?.reason ? <>
                    <Descriptions.Item label={data?.status === 6 ? '拒绝原因' : '失败原因'}>{data?.reason}</Descriptions.Item>
                </> : <></>}
            </Descriptions>
        </ModalForm>
    );
};