$(document).ready(function(){
    $('.basket-item .image img').click(function(){
        showBox(baseUrl + '/produces/reviewitem/id/' + $(this).parents('.basket-item').data('basket-item-id'));
    });
    $('.decrease-count').click(function(){
        incrisItemCount($(this), -1);

        return false;
    });
    $('.increase-count').click(function(){
        incrisItemCount($(this), 1);

        return false;
    });
    $('#create-order-btn').click(function(){
        var url = '';
        $('.basket-item').each(function() {
            url += (url == '')
                ? baseUrl + '/user/create-order?items=' + parseInt($(this).data('basket-item-id'))
                : ',' + parseInt($(this).data('basket-item-id'));
        });
        if(url !== '') {
            document.location.href = url;
        }
    })

    function incrisItemCount(that, increasTo){
        var currentItemId = that.parents('.basket-item').data('basket-item-id');
        var counterTextObj = that.parents('.basket-item').find('.current-count');
        var currentItemCount = parseInt(counterTextObj.attr('value'));
        var newCount = currentItemCount + increasTo
        if(newCount >= 1){
            counterTextObj.attr('value', newCount);
            $.ajax({
                type: "POST",
                url: baseUrl + "/user/change-item-count",
                data: {
                    itemId: currentItemId,
                    itemCount: newCount
                },
                success: function() {
                    countOrderDetails();
                }
            });
        }
    }
});

function countOrderDetails(){
    var totalCount = 0;
    $('.basket-item').each(function() {
        var count = parseInt($(this).find('.current-count').attr('value'));
        var pricePerItem = parseFloat($(this).find('.price-per-item').text()).toFixed(2);
        $(this).find('.item-total-price span').text((count * pricePerItem).toFixed(2));
        totalCount += count;
    });
    $('#total-count').text(totalCount);
    $('#basket-items-count').text(totalCount);

    var totalPrice = 0;
    $('.basket-item .item-total-price span').each(function() {
        totalPrice += parseFloat($(this).text());
    });
    $('#total-amount').text(totalPrice.formatMoney(2));
}
