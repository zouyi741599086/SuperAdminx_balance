import { useRef } from 'react';
import { PageContainer } from '@ant-design/pro-components';
import { balanceDetailsApi } from '@/api/balanceDetails';
import { balanceApi } from '@/api/balance';
import { fileApi } from '@/api/file';
import { ProTable } from '@ant-design/pro-components';
import { App, Button, Typography, Space, Tooltip, Avatar, Tag } from 'antd';
import {
    CloudDownloadOutlined,
} from '@ant-design/icons';
import { authCheck } from '@/common/function';
import SelectUser from '@/components/selectUser';

/**
 * 用户余额明细 
 *
 * @author zy <741599086@qq.com>
 * @link https://www.superadminx.com/
 * */
export default () => {
    const { message } = App.useApp();
    const tableRef = useRef();
    const formRef = useRef();

    // 刷新表格数据
    const tableReload = () => {
        tableRef.current.reload();
        tableRef.current.clearSelected();
    }

    /////////////////////////导出////////////////////////
    const exportData = () => {
        message.open({
            type: 'loading',
            content: '数据生成中...',
            duration: 0,
            key: 'excel'
        });
        let params = formRef.current.getFieldsValue();
        balanceDetailsApi.exportData(params).then(res => {
            message.destroy('excel')
            if (res.code === 1 && res.data.filePath && res.data.fileName) {
                message.success('数据已生成');
                setTimeout(() => {
                    window.open(`${fileApi.download}?filePath=${res.data.filePath}&fileName=${res.data.fileName}`);
                }, 1000)
            } else {
                message.error('数据导出失败');
            }
        })
    }

    // 表格列
    const columns = [
        {
            title: '用户',
            dataIndex: 'user_id',
            search: true,
            valueType: 'selectTable',
            renderFormItem: () => <SelectUser />,
            render: (_, record) => {
                if (record.User) {
                    return <div style={{ display: 'flex' }}>
                        <Avatar
                            src={record.User?.img}
                            style={{
                                flexShrink: 0
                            }}
                        >{record.User?.name?.substr(0, 1)}</Avatar>
                        <div style={{ paddingLeft: '5px' }}>
                            {record.User?.name}<br />
                            <Typography.Paragraph>{record.User?.tel}</Typography.Paragraph>
                        </div>
                    </div>
                }
                return '--';
            },
        },
        {
            title: '余额类型',
            dataIndex: 'balance_type',
            search: true,
            valueType: 'select',
            request: async () => {
                const result = await balanceApi.getBalanceType();
                let list = [];
                Object.keys(result.data).forEach(key => {
                    list.push({
                        value: key,
                        label: result.data[key],
                    })
                })
                return list;
            },
            fieldProps: {
                showSearch: true,
            },
            //render: (_, record) => 
        },
        {
            title: '标题',
            dataIndex: 'title',
            search: true,
            valueType: 'text',
            render: (_, record) => _,
        },
        {
            title: '类型',
            dataIndex: 'type',
            search: true,
            valueType: 'select',
            fieldProps: {
                showSearch: true,
                options: [
                    {
                        value: 1,
                        label: '增加',
                    },
                    {
                        value: 2,
                        label: '减少',
                    },
                ]
            },
            render: (_, record) => <>
                {record.type === 1 ? <>
                    <Tag bordered={false} color="processing">增加</Tag>
                </> : ''}
                {record.type === 2 ? <>
                    <Tag bordered={false} color="success">减少</Tag>
                </> : ''}
            </>
        },
        {
            title: '变更值',
            dataIndex: 'change_value',
            search: false,
            sorter: true,
            render: (_, record) => <>
                <Typography.Text type="danger">{record.change_value}</Typography.Text>
            </>
        },
        {
            title: '变更后余额',
            sorter: true,
            dataIndex: 'change_balance',
            search: false,
            render: (_, record) => <>
                <Typography.Text type="success">{record.change_balance}</Typography.Text>
            </>
        },
        {
            title: '变化时间',
            dataIndex: 'create_time',
            search: true,
            sorter: true,
            valueType: 'dateRange',
            render: (_, record) => record.create_time,
        },

    ];
    return (
        <>
            <PageContainer
                className="sa-page-container"
                ghost
                header={{
                    title: '用户余额明细',
                    style: { padding: '0px 24px 12px' },
                }}
            >
                <ProTable
                    actionRef={tableRef}
                    formRef={formRef}
                    rowKey="id"
                    columns={columns}
                    scroll={{
                        x: 1000
                    }}
                    options={{
                        fullScreen: true
                    }}
                    columnsState={{
                        // 此table列设置后存储本地的唯一key
                        persistenceKey: 'table_column_' + 'BalanceDetails',
                        persistenceType: 'localStorage'
                    }}
                    headerTitle={
                        <Space>
                            <Tooltip title="根据当前搜索条件导出数据~">
                                <Button
                                    type="primary"
                                    danger
                                    ghost
                                    icon={<CloudDownloadOutlined />}
                                    onClick={exportData}
                                    disabled={authCheck('balanceDetailsExportData')}
                                >导出</Button>
                            </Tooltip>
                        </Space>
                    }
                    pagination={{
                        defaultPageSize: 20,
                        size: 'default',
                        // 支持跳到多少页
                        showQuickJumper: true,
                        showSizeChanger: true,
                        responsive: true,
                    }}
                    request={async (params = {}, sort, filter) => {
                        // 排序的时候
                        let orderBy = '';
                        for (let key in sort) {
                            orderBy = key + ' ' + (sort[key] === 'descend' ? 'desc' : 'asc');
                        }
                        const result = await balanceDetailsApi.getList({
                            ...params,// 包含了翻页参数跟搜索参数
                            orderBy, // 排序
                            page: params.current,
                        });
                        return {
                            data: result.data.data,
                            success: true,
                            total: result.data.total,
                        };
                    }}

                />
            </PageContainer>
        </>
    )
}
