var vkApiId = 2228139;
var vkFBId = 521117277930288;
var VK_user_id = null;
var FB_user_id = null;
var capthaVKId = null;

$(document).ready(function () {
    $("#post-to-vk").click(function () {
        capthaVKId = null;
        deleteVKAppKeyCookie();
        VK.init({apiId: vkApiId});
        VK.Auth.getLoginStatus(function (response) {
            if (response.session) {
                VK_user_id = response.session.mid;
                postInVK();
            } else {
                loginInVK();
            }
        });
    });
    $("#post-to-fb").click(function () {
        FB.init({appId: vkFBId, status: true, cookie: true, xfbml: true});
        FB.getLoginStatus(function (response) {
            if (response.status == "connected") {
                FB_user_id = response.authResponse.userID;
                postInFB();
            } else {
                FB.login(function (response) {
                    if (response.session) {
                        FB_user_id = response.session.uid;
                        postInFB();
                    }
                });
            }
        });
    });
    $("#vk-captcha-yes-btn").click(function () {
        postInVK();
        $("#vk-popup").hide();
    });
    $("#vk-captcha-no-btn").click(function () {
        $("#vk-popup").hide();
    });
    $("#set-social-network").click(function () {
        $("#settings-tabs li").removeClass("f-active");
        $("#settings-tabs a[data-el-id=edit-servises-group]").parent('li').addClass("f-active");
        $("#" + $("#settingsTabInfo").val()).hide();
        $("#" + $("#settingsTabInfo").val() + "-hlp").hide();
        $("#edit-servises-group").show();
        $("#edit-servises-group-hlp").show();
        $("#settingsTabInfo").val("edit-servises-group");
        return false;
    });
});

function deleteVKAppKeyCookie() {
    var cookiesArray = document.cookie.split(/;/gi);
    var cookiesStr = '';
    for (var i in cookiesArray) {
        if (!( cookiesArray[i].match(/vk_app_2433150/gi))) {
            cookiesStr += (cookiesStr == "") ? cookiesArray[i] : ";" + cookiesArray[i];
        }
    }
    document.cookie = cookiesStr;
}

function loginInVK() {
    VK.Auth.login(
        function (response) {
            if (response.session) {
                VK_user_id = response.session.mid;
                postInVK();
            }
        }
    );
}

function postInVK() {
    var params = {
        owner_id: VK_user_id,
        message: wallPostMessage + " photoprint.in.ua/?refKey=" + $("#full-url-ref").attr("data-url-ref-code"),
//        attachment: "http:://photoprint.in.ua/public/theme/logo.png"
    };
    if (capthaVKId != null) {
        params.captcha_sid = capthaVKId;
        params.captcha_key = $("#vk-captche-text").val();
    }
    VK.api(
        'wall.post',
        params,
        function (response) {
            if (isDefined(response.error)) {
                if (response.error.error_code == 14) {
                    capthaVKId = response.error.captcha_sid;
                    $("#vk-captche-img").attr("src", response.error.captcha_img);
                    $("#vk-captche-text").val("");
                    var bodySize = getClientSize();
                    var scrolSize = 0;
                    if ($(document).height() > $(window).height()) {
                        scrolSize = scrollbarWidth();
                    }
                    $("#vk-popup").css({
                        left: ((bodySize[0] - scrolSize - $("#vk-popup").width()) / 2 > 0) ? (bodySize[0] - scrolSize - $("#vk-popup").width()) / 2 : 0,
                        top: ((bodySize[1] - $("#vk-popup").height()) / 4 > 0) ?
                            (bodySize[1] - $("#vk-popup").height()) / 4 + $(window).scrollTop() : $(window).scrollTop()
                    });
                    $("#vk-popup").show();
                }
            } else {
                //TODO savain db
            }
        }
    );
}

function postInFB() {
    FB.ui(
        {
            display: 'popup'
            , method: "stream.publish"
            , message: wallPostMessage
//            , attachment: {
//                name: wallImagePostTitle
//                , href: "http://photoprint.in.ua/public/theme/logo.png"
//                , caption: wallImagePostMessage
//            }
//            , action_links: JSON.stringify("http://photoprint.in.ua/?refKey=" + $("#full-url-ref").attr("data-url-ref-code"))
        },
        function (response) {
            if (response && response.post_id) {
                //TODO savain db
            }
        }
    );
}