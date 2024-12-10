import { useRef, useState } from 'react';
import {
    ModalForm,
} from '@ant-design/pro-components';
import { balanceApi } from '@/api/balance';
import { App } from 'antd';
import { useUpdateEffect } from 'ahooks';
import { ProFormDigit, ProFormSelect, ProFormText, ProFormRadio } from '@ant-design/pro-components';

/**
 * 用户余额 修改
 *
 * @author zy <741599086@qq.com>
 * @link https://www.superadminx.com/
 * */
export default ({ tableReload, balanceType, updateBalanceUserId, setUpdateBalanceUserId, ...props }) => {
    const formRef = useRef();
    const { message } = App.useApp();
    const [open, setOpen] = useState(false);

    useUpdateEffect(() => {
        if (updateBalanceUserId > 0) {
            setOpen(true);
        }
    }, [updateBalanceUserId])

    return <>
        <ModalForm
            name="updateBalance"
            formRef={formRef}
            open={open}
            onOpenChange={(_boolean) => {
                setOpen(_boolean);
                // 关闭的时候干掉updateBalanceUserId，不然无法重复修改同一条数据
                if (_boolean === false) {
                    setUpdateBalanceUserId(0);
                }
            }}
            title="变更用户余额"
            width={400}
            // 第一个输入框获取焦点
            autoFocusFirstInput={true}
            // 可以回车提交
            isKeyPressSubmit={true}
            // 不干掉null跟undefined 的数据
            omitNil={false}
            modalProps={{
                destroyOnClose: true,
            }}
            onFinish={async (values) => {
                const result = await balanceApi.updateBalance({
                    user_id: updateBalanceUserId,
                    ...values
                });
                if (result.code === 1) {
                    tableReload?.();
                    message.success(result.message)
                    formRef.current?.resetFields?.()
                    return true;
                } else {
                    message.error(result.message)
                }
            }}
        >
            <ProFormSelect
                name="balance_type"
                label="变更余额类型"
                placeholder="请输入"
                request={async () => {
                    let options = [];
                    Object.keys(balanceType).forEach(key => {
                        options.push({
                            value: key,
                            label: balanceType[key],
                        })
                    })
                    return options;
                }}
                rules={[
                    { required: true, message: '请输入' },
                ]}
            />
            <ProFormRadio.Group
                name="type"
                label="变更类型"
                placeholder="请选择"
                options={[
                    { label: '增加', value: 1 },
                    { label: '减少', value: 2 },
                ]}
                rules={[
                    { required: true, message: '请选择' },
                ]}
            />
            <ProFormDigit
                name="change_value"
                label="变更值"
                placeholder="请输入"
                fieldProps={{
                    precision: 2,
                    style: { width: '100%' },
                }}
                min={0.01}
                rules={[
                    { required: true, message: '请输入' },
                    { type: 'number', min: 0.01, message: '请输入正确的值' },
                ]}
            />
            <ProFormText
                name="title"
                label="变更原因"
                placeholder="请输入"
                rules={[
                    { required: true, message: '请输入' },
                ]}
            />

        </ModalForm>
    </>;
};