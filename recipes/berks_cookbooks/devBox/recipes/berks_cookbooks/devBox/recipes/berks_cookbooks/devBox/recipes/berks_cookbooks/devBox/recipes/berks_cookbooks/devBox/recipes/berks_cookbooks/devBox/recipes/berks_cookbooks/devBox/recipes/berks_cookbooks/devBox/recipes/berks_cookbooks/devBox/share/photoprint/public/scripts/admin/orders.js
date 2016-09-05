/**
 * micko
 */
$(document).ready(function () {
    $('.editStatusBtn').click(function () {
        $(this).hide();
        $(this).parent('div').find('.saveStatusBtn').show();
        $(this).parents('tr').find('.currentStatus').hide();
        $(this).parents('tr').find('.changeStatus').show();

    });
    $('.saveStatusBtn').click(function () {
        $(this).hide();
        $(this).parent('div').find('.editStatusBtn').show();
        $(this).parents('tr').find('.changeStatus').hide();
        $(this).parents('tr').find('.currentStatus').show();
        $(this).parents('tr').find('.currentStatus').html(
            $(this).parents('tr').find('.changeStatus').children("select.changeStatus option:selected").text()
        )
        var id = $(this).parents('tr').find('.itemId').html().trim();
        var status = $(this).parents('tr').find('.changeStatus').children("select.changeStatus option:selected").val();
        $.getJSON(
            baseUrl + '/admin/changeitemstatus',
            {itemId: id, itemStatus: status},
            function (data) {
            }
        );
    });

    $('.editOrderStatusBtn').click(function () {
        $(this).hide();
        $(this).parent('span').find('.saveOrderStatusBtn').show();
        $(this).parents('.orderStatus').find('.currentOrderStatus').hide();
        $(this).parents('.orderStatus').find('.changeOrderStatus').show();
    });

    $('.saveOrderStatusBtn').click(function () {
        $(this).hide();
        $(this).parent('span').find('.editOrderStatusBtn').show();
        $(this).parents('.orderStatus').find('.changeOrderStatus').hide();
        $(this).parents('.orderStatus').find('.currentOrderStatus').show();
        $(this).parents('.orderStatus').find('.currentOrderStatus').html(
            $(this).parents('.orderStatus').find('.changeOrderStatus').children("select.changeOrderStatus option:selected").text()
        )
        var id = $(this).parents('.orderStatus').find('.orderId').html().trim();
        var status = $(this).parents('.orderStatus').find('.changeOrderStatus').children("select.changeOrderStatus option:selected").val();
        $.getJSON(
            baseUrl + '/admin/change-order-status',
            {orderId: id, orderStatus: status},
            function (data) {
                if (data.order_status == 'canceled' && tracking) {
                    ga('require', 'ecommerce');
                    ga('ecommerce:addTransaction', {
                        id: data.order.id,
                        revenue: -data.order.payment,
                        currency: 'UAH'
                    });
                    for (var i = 0, len = data.items.length; i < len; i++) {
                        ga('ecommerce:addItem', {
                            id: data.order.id,
                            name: data.items[i].name,
                            sku: data.items[i].id,
                            price: data.items[i].payment,
                            currency: 'UAH',
                            quantity: -data.items[i].count
                        });
                    }
                    ga('ecommerce:send');
                }
            }
        );
        if ((status == "closed") || (status == "canceled")) {
            $(".o_" + id).hide();
        }
    });
});
