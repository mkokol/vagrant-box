$(document).ready(function(){
    loadProductPage();
});

function loadProductPage(){
    if($("#accept-shop-rules").val() == "0"){
        loadAcceptRulesPage();
    }else if($("#accept-shop-rules").val() == "1"){
        if(isDefined($.currentUrl().param("type")) && $.currentUrl().param("type") == "balance"){
            loadBalanceDeteils();
        } else if(isDefined($.currentUrl().param("type")) && $.currentUrl().param("type") == "ref"){
            loadReferralsDeteils();
        } else if(isDefined($.currentUrl().param("type")) && $.currentUrl().param("type") == "public"){
            loadPublicShopDeteils();
        } else if(isDefined($.currentUrl().param("type")) && $.currentUrl().param("type") == "private"){
            loadPrivateShopDeteils();
        } else {
            loadStartPage();
        }
    }
}

function reLoadProductPage(){
    loadProductPage();
}
function loadAcceptRulesPage(){
    $("#partnership").load(
        baseUrl + "/partnership/info?v=" + baseVersion,
        function(){
            $("#accept-rules").click(function(){
                if($('#agreed-with-rules').is(':checked')){
                    $.ajax({
                        type: "POST",
                        url: baseUrl + "/partnership/acceptrules",
                        data: {},
                        success: function(data) {
                            loadStartPage();
                        }
                    });
                } else {
                    alert("rules_not_accepted");
                }
            });
        }
    );
}
function loadStartPage(){
    $("#partnership").load(
        baseUrl + "/partnership/types?v=" + baseVersion,
        function(){
            $("#referrals-deteils").click(function(){
                loadReferralsDeteils();
            });
            $("#public-shop-deteils").click(function(){
                loadPublicShopDeteils();
            });
        }
    );
}
function loadBalanceDeteils(){
    $("#partnership").load(
        baseUrl + "/partnership/balance?v=" + baseVersion,
        function(){
            $('.h-nav li').removeClass('selected');
            $('.h-nav li.balance').addClass('selected');
            $.currentUrl().push("type", "balance");
            $.currentUrl().update();
            reloadBox();
        }
    );
}
function loadReferralsDeteils(){
    $("#partnership").load(
        baseUrl + "/partnership/referrals?v=" + baseVersion,
        function(){
            $('.h-nav li').removeClass('selected');
            $('.h-nav li.ref').addClass('selected');
            $.currentUrl().push("type", "ref");
            $.currentUrl().update();
        }
    );
}
function loadPublicShopDeteils(){
    $("#partnership").load(
        baseUrl + "/partnership/public?v=" + baseVersion,
        function(){
            $('.h-nav li').removeClass('selected');
            $('.h-nav li.public').addClass('selected');
            $.currentUrl().push("type", "public");
            $.currentUrl().update();
            loadProducts("public", "", 1);
        }
    );
}
function loadProducts(type, group, page){
    var loadPath = (group == "") ?
        baseUrl + "/partnership/loadproducts?page=" + page + "&v=" + baseVersion :
        baseUrl + "/partnership/loadproducts?groupId=" + group + "&page=" + page + "&v=" + baseVersion;

    $("#shop-content-products").load(
        loadPath,
        function(){
            $('.paging').click(function (){
                var pageNumner = $(this).attr('data-page-number');
                loadProducts(type, group, pageNumner);
                return false;
            });
        }
    );
}
