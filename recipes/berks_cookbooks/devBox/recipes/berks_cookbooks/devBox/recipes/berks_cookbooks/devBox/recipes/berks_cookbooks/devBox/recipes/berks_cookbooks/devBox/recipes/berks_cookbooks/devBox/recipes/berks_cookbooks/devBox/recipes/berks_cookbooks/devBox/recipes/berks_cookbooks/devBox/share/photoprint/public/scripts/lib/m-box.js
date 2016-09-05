/******************************************************************************
 * micko
 *
 * building pop ups
 *****************************************************************************/
var progresImage = basePath + "/public/theme/ajaxLoader.gif";
var closeImage = basePath + "/public/theme/mBoxClose.png";
var startRte = true;
var temporaryMboxWidth = 200;
var temporaryMboxHeight = 150;
var mboxInProgres = false;

$(document).ready(function(){
    loadBox();//pass where to apply thickbox
    imgProgres = new Image();// preload image
    imgProgres.src = progresImage;
});
function loadBox(){
    $('a.mbox').click(initBox);
}
function reloadBox(){
    $('a.mbox').unbind('click', initBox);
    loadBox();
}
function initBox(){
    if(isDefined($(this).data('url'))) {
        showBox($(this).data('url'));
    } else if(isDefined(this.href)) {
        showBox(this.href)
        this.blur();
    } else {
        console.log('url is not provided');
    }

    return false;
}
function setProgres(w){
    $("#progresBar").width(2 * w);
}
function showBox(url){
    mboxInProgres = true;

    if( ! ($("#mbox-overlay").size() > 0)) {
        $("body").append("<div id='mbox-overlay'></div>");
        $("body").append("<div id='mbox-wnd'></div>");
        $("body").append("<img id=\"image-loader\" src=\"" + progresImage + "\" />");
    }

    var bodySize = getClientSize();
    $("#image-loader").css({left: bodySize[0] / 2 - 25, top: bodySize[1] / 2 - 25});
    $("#mbox-wnd").css({left: (bodySize[0] - temporaryMboxWidth)/2, top: (bodySize[1] - temporaryMboxHeight)/2});
    $("#mbox-wnd").width(temporaryMboxWidth);
    $("#mbox-wnd").height(temporaryMboxHeight);

    $.ajax({
        type: "POST",
        url: url,
        data: {},
        success: function(data) {
            $("#mbox-wnd").html(data);
            $('#mbox-wnd').animate(
                {
                    left: ((bodySize[0] - parseInt($("#popup-wnd-data-content").css('width')))/2)
                    , top: ((bodySize[1] - parseInt($("#popup-wnd-data-content").css('height')))/2)
                    , height: $("#popup-wnd-data-content").css('height')
                    , width: $("#popup-wnd-data-content").css('width')
                }
                , 500
                , function() {
                    $("#image-loader").hide();
                    $("#image-loader").remove();
                    $("body").append("<img id=\"mbox-wnd-close\" src=\"" + closeImage + "\"/>");
                    $("#mbox-wnd-close").css({
                        left: (bodySize[0] + parseInt($("#popup-wnd-data-content").css('width')))/2 - 34
                        , top: (bodySize[1] - parseInt($("#popup-wnd-data-content").css('height')))/2 + 11
                    });
                    $("#mbox-wnd,#mbox-wnd-close").mouseover(function() {
                        $("#mbox-wnd-close").show();
                        if($(this).attr("id") === "mbox-wnd-close"){
                            $("#mbox-wnd-close").css({"opacity": 1});
                        } else {
                            $("#mbox-wnd-close").css({"opacity": 0.6});
                        }
                    });
                    $("#mbox-wnd").mouseout(function(){
                        $("#mbox-wnd-close").hide();
                    });
                    document.onkeyup = function(e){
                        keycode = (e == null) ? event.keyCode : keycode = e.which;
                        if(keycode == 27){
                            mboxRemove();
                        }
                    };
                    $("body #mbox-overlay, #mbox-wnd-close, .close-popup-wnd").click(function(){
                        mboxRemove();
                    });
                    $("#popup-wnd-data-content").show( 1, function(){
                        if($("#popup-wnd-scroll-panel").length > 0){
                            $("#popup-wnd-scroll-panel").jScrollPane({ showArrows: true });
                        }
                    });
                    $(window).resize(function() {
                        var bodySize = getClientSize();
                        $("#mbox-wnd-close").css({
                            left: (bodySize[0] + parseInt($("#popup-wnd-data-content").css('width')))/2 - 34
                            , top: (bodySize[1] - parseInt($("#popup-wnd-data-content").css('height')))/2 + 11
                        });
                        $('#mbox-wnd').css({
                            left: ((bodySize[0] - parseInt($("#popup-wnd-data-content").css('width')))/2)
                            , top: ((bodySize[1] - parseInt($("#popup-wnd-data-content").css('height')))/2)
                        });
                    });
                    mboxInProgres = false;
                }
            );
        }
    });
}
function mboxRemove(){
    $("#mbox-overlay").hide();
    $("#mbox-wnd").hide();
    $("#mbox-wnd-close").hide();
    $("#mbox-overlay").remove();
    $("#mbox-wnd").remove();
    $("#mbox-wnd-close").remove();
};
function showTextEditorBox(ContentId, MBOX_WIDTH, MBOX_HEIGHT){
    if( ! ($("#mbox-overlay").size() > 0)) {
        $("body").append("<div id='mbox-overlay'></div>");
    }
    var bodySize = getClientSize();
    $("#text-editor").width(MBOX_WIDTH);
    $("#text-editor").height(MBOX_HEIGHT);
    $("#text-editor").css({left: (bodySize[0] - MBOX_WIDTH)/2, top: (bodySize[1] - MBOX_HEIGHT)/2, display: ''});
    if(startRte) {
        startRte = false;
    } else {
        document.getElementById(rteName).contentWindow.document.body.innerHTML = $("#postcardText").html();
    }
    document.onkeyup = function(e){
        keycode = (e == null) ? event.keyCode : keycode = e.which;
        if(keycode == 27){ // close
            $("#mbox-overlay").hide();
            $("#mbox-overlay").remove();
            $("#text-editor").css({display: 'none'});
        }
    };
    $("body #mbox-overlay").click(function(){
        $("#mbox-overlay").hide();
        $("#mbox-overlay").remove();
        $("#text-editor").css({display: 'none'});
    });
}
