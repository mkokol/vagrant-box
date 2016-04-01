$(document).ready(function(){
    reloadBox();
    loadProducts('', 1);
    $(".shop-product-theme").click(function(){
        $(".shop-product-theme").removeClass('selected');
        $(this).addClass("selected");
        loadProducts($(this).attr("data-group-id"), 1);
    });
    $("#create-more-product").click(function(){
        if($(".product-categories .selected").attr("data-group-id") == ""){
            showBox(this.href);
            this.blur();
        } else{
            location.href =  baseUrl + '/wl/create/group/' + $(".product-categories .selected").attr("data-group-name")
                + '/code/' + $('#shop-content').attr('data-wl-code');
        }
        return false;
    });
});
function reloadProducts(){
    loadProducts($(this).attr("data-group-id"), 1);
}
function loadProducts(group, page){
    var loadPath = baseUrl + '/wl/loadproducts/code/' + $('#shop-content').attr('data-wl-code') + '?page=' + page;
    loadPath += (group) ? '&groupId=' + group : '';
    loadPath += '&v=' + baseVersion;
    $("#shop-content-products").load(
        loadPath,
        function(){
            $('.paging').click(function (){
                var pageNumner = $(this).attr('data-page-number');
                loadProducts(group, pageNumner);
                return false;
            });
        }
    );
}