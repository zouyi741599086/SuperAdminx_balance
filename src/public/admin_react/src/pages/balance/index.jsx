import { useRef, lazy, useState, useEffect } from 'react';
import { PageContainer } from '@ant-design/pro-components';
import { balanceApi } from '@/api/balance';
import { ProTable } from '@ant-design/pro-components';
import { App, Button, Typography, Space, Tooltip, Avatar } from 'antd';
import {
    CloudDownloadOutlined,
} from '@ant-design/icons';
import { authCheck } from '@/common/function';
import { fileApi } from '@/api/file';
import Lazyload from '@/component/lazyLoad/index';
import SelectUser from '@/components/selectUser';
import { useMount } from 'ahooks';

const UpdateBalance = lazy(() => import('./updateBalance'));

/**
 * 用户余额 
 *
 * @author zy <741599086@qq.com>
 * @link https://www.superadminx.com/
 * */
export default () => {
    const { message } = App.useApp();
    const tableRef = useRef();
    const formRef = useRef();

    useMount(() => {
        getBalanceType();
    })

    // 刷新表格数据
    const tableReload = () => {
        tableRef.current.reload();
        tableRef.current.clearSelected();
    }

    // 变更余额的用户id
    const [updateBalanceUserId, setUpdateBalanceUserId] = useState();

    // 余额的类型配置
    const [balanceType, setBalanceType] = useState([])
    const getBalanceType = () => {
        balanceApi.getBalanceType().then(res => {
            if (res.code === 1) {
                console.log(res.data);
                setBalanceType(res.data);
            } else {
                message.error(res.message);
            }
        })
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
        balanceApi.exportData(params).then(res => {
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
    const [columns, setColumns] = useState([
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
            title: '变更时间',
            dataIndex: 'update_time',
            search: true,
            sorter: true,
            valueType: 'dateRange',
            render: (_, record) => record.create_time,
        },
        {
            title: '操作',
            dataIndex: 'action',
            render: (_, record) => <>
                <Button
                    type="link"
                    size="small"
                    disabled={authCheck('updateBalance')}
                    onClick={() => {
                        setUpdateBalanceUserId(record.user_id);
                    }}
                >变更余额</Button>
            </>,
        },
    ]);
    useEffect(() => {
        let newColumns = [];
        Object.keys(balanceType).forEach(key => {
            newColumns.push({
                title: balanceType[key],
                dataIndex: key,
                search: false,
                sorter: true,
                render: (_, record) => <>
                    <Typography.Text type="danger">{record[key]}</Typography.Text>
                </>
            })
        })

        let _columns = [...columns];
        _columns.splice(1, 0, ...newColumns);
        setColumns(_columns);
    }, [balanceType])

    return (
        <>
            {/* 变更余额 */}
            <Lazyload block={false}>
                <UpdateBalance
                    tableReload={tableReload}
                    balanceType={balanceType}
                    updateBalanceUserId={updateBalanceUserId}
                    setUpdateBalanceUserId={setUpdateBalanceUserId}
                />
            </Lazyload>
            <PageContainer
                className="sa-page-container"
                ghost
                header={{
                    title: '用户余额',
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
                        persistenceKey: 'table_column_' + 'Balance',
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
                                    disabled={authCheck('balanceExportData')}
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
                        const result = await balanceApi.getList({
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
