
export default {
    PAY_STATUS: [
        {
            value: 0,
            label: '未支付'
        },
        {
            value: 1,
            label: '已支付'
        }
    ],
    STATUS: [
        {
            value: 0,
            label: '待配送'
        },
        {
            value: 1,
            label: '配送中'
        },
        {
            value: 2,
            label: '已完成'
        }
    ],
    REFUND_STATUS: [
        {
            value: 1,
            label: '退款待审核'
        },
        {
            value: 2,
            label: '退款成功'
        },
        {
            value: 3,
            label: '退款失败'
        }
    ],
    ORDER_QUERY: { // 订单状态条件
        'ALL': [{ key: 'refund_status', sign: '=', value: 0 }],
        'WAITPAY': [
            { key: 'cancel_at', sign: '=', value: 0 },
            { key: 'refund_status', sign: '=', value: 0 },
            { key: 'status', sign: '=', value: 0 },
            { key: 'pay_at', sign: '=', value: 0 }
        ],
        'WAYSEND': [
            { key: 'cancel_at', sign: '=', value: 0 },
            { key: 'refund_status', sign: '=', value: 0 },
            { key: 'status', sign: '=', value: 0 },
            { key: 'pay_at', sign: '>', value: 0 }
        ],
        'HADSEND': [
            { key: 'cancel_at', sign: '=', value: 0 },
            { key: 'refund_status', sign: '=', value: 0 },
            { key: 'status', sign: '=', value: 1 },
            { key: 'pay_at', sign: '>', value: 0 }
        ],
        'COMPLETE': [
            { key: 'cancel_at', sign: '=', value: 0 },
            { key: 'refund_status', sign: '=', value: 0 },
            { key: 'status', sign: '=', value: 2 },
            { key: 'pay_at', sign: '>', value: 0 }
        ],
        'CLOSE': [
            { key: 'cancel_at', sign: '>', value: 0 },
            { key: 'refund_status', sign: '=', value: 0 }
        ]
    },
    REFUND_ORDER_QUERY: { // 订单状态条件
        'ALL': [],
        'WAITREFUND': [
            { key: 'refund_status', sign: '=', value: 0 }
        ],
        'COMPLETE': [
            { key: 'refund_status', sign: '=', value: 1 }
        ],
        'FAILREFUND': [
            { key: 'refund_status', sign: '=', value: 2 }
        ],
    },

    WAYBILL_QUERY: { //运单状态条件
        'ALL': [],
        'WAITPICK': [{ key: 'status', sign: '=', value: 0 }],
        'INDELIVER': [{ key: 'status', sign: '=', value: 1 }],
        'COMPLETE': [{ key: 'status', sign: '=', value: 2 }]
    },

    // 订单状态
    ORDER_STATUS(item) {
        if(!Array.isArray(item)) {
            item = [item];
        }
        item.forEach(ele => {
            if (ele.cancel_at == 0 && ele.refund_status == 0) {
                if (ele.status == 0 && ele.pay_at == 0) {
                    ele.state = 'WAITPAY'
                    ele.statusVal = '待付款'
                    ele.color = '#eb4d53'
                } else if (ele.status == 0 && ele.pay_at > 0) {
                    ele.state = 'WAYSEND'
                    ele.statusVal = '待发货'
                    ele.color = '#ffa929'
                } else if (ele.status == 1 && ele.pay_at > 0) {
                    ele.state = 'HADSEND'
                    ele.statusVal = '待收货'
                    ele.color = '#298df8'
                } else if (ele.status == 2 && ele.pay_at > 0) {
                    ele.state = 'COMPLETE'
                    ele.statusVal = '已完成'
                    ele.color = '#3ba53a'
                }
            } else if (ele.cancel_at > 0) {
                ele.state = 'CLOSE'
                ele.statusVal = '已关闭'
                ele.color = '#a074c4'
            }
        })
        return item
    },

    //售后订单状态
    REFUND_STATUS(item) {
        if(!Array.isArray(item)) {
            item = [item];
        }
        item.forEach(ele => {
            if (ele.refund_status == 0) {
                ele.state = 'WAITREFUND'
                ele.statusVal = '待处理'
                ele.color = '#ed6369'
            } else if (ele.refund_status == 1) {
                ele.state = 'COMPLETE'
                ele.statusVal = '退款成功'
                ele.color = '#add580'
            } else if (ele.refund_status == 2) {
                ele.state = 'FAILREFUND'
                ele.statusVal = '退款失败'
                ele.color = '#ed6369'
            }
        })
        return item
    }
};
