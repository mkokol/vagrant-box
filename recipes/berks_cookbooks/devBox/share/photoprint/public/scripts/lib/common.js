/******************************************************************************
 * micko
 *
 * global functions
 *****************************************************************************/

function isDefined (value) {
    return ((typeof(value) != "undefined") && (value != null) && (value != "null") && (value != undefined));
}
function getClientSize() {
    var myWidth = 0, myHeight = 0;
    if( typeof( window.innerWidth ) == 'number' ) {
        //Non-IE
        myWidth = window.innerWidth;
        myHeight = window.innerHeight;
    } else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
        //IE 6+ in 'standards compliant mode'
        myWidth = document.documentElement.clientWidth;
        myHeight = document.documentElement.clientHeight;
    } else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
        //IE 4 compatible
        myWidth = document.body.clientWidth;
        myHeight = document.body.clientHeight;
    }
    return [myWidth, myHeight];
}
function showSide(side) {
    if(side == 'front') {
        $("#tshirt-back").hide();
        $("#tshirt-front").show();
    }
    if(side == 'back') {
        $("#tshirt-front").hide();
        $("#tshirt-back").show();
    }
}
function handleProductPreview(){
    $('.preview').unbind('click').click(function(){
        document.location.href = $(this).find('.view-item').attr('href');
    });
}
var googleAnalytics = {
    trackEvent: function trackEvent(category, action, label, value) {
        var param = {
            'eventCategory': category,
            'eventAction': action,
            'hitCallback': function() {},
            'hitCallbackFail' : function () {}
        };
        if (label) {
            param.eventLabel = label;
        }
        if (value) {
            param.eventValue = value;
        }
        if (tracking) {
            ga('send', 'event', param);
        }
    }
};
$(document).ready(function(){
    $('.ga-event-tracking').click(function(){
        googleAnalytics.trackEvent(
            $(this).attr('data-ga-category'),
            $(this).attr('data-ga-action'),
            $(this).attr('data-ga-label'),
            $(this).attr('data-ga-value'),
            $(this).attr('href')
        );
    });
    if($('ul#tabs li a').length) {
        $('ul#tabs li a').featureList({output : '#output li', start_item : 0});
    }
    $('#show-login-wnd-btn').click(function(){
        showBox(baseUrl + '/user/login-wnd');
    });
    $('.img-products-promo').click(function () {
        document.location.href = $('.' + $(this).data('link-class')).attr('href');
    });
    $('.preview-product-images').click(function(){
        showBox(baseUrl + '/produces/productpreview/item/' + $(this).data('product-item'));
    });
    $('#view-hide-group').bind('touchstart click', function (e) {
        e.preventDefault();
        if ($('nav.v-nav ul').hasClass('view-all')) {
            $('nav.v-nav ul').removeClass('view-all');
            $(this).text('+');
        } else {
            $('nav.v-nav ul').addClass('view-all');
            $(this).text('-');
        }
        return false;
    });
    $('.t-shirt-sizes-grid').click(function(){
        showBox(baseUrl + '/produces/t-shirt-sizes/type/' + $(this).data('product-item'));
    });
    handleProductPreview();
});
Number.prototype.formatMoney = function(c, d, t){
    var n = this,
        c = isNaN(c = Math.abs(c)) ? 2 : c,
        d = d == undefined ? "." : d,
        t = t == undefined ? "," : t,
        s = n < 0 ? "-" : "",
        i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
        j = (j = i.length) > 3 ? j % 3 : 0;
    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};