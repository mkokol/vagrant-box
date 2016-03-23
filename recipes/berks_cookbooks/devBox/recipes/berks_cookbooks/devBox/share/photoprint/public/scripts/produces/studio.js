var loaderImg = basePath + "/public/theme/ajaxLoader.gif";
var dragTarget = null;
var pic_coll = new Array();
var resizeTimer = false;
var speedZoom = 5;
var editableEl = null;
var isMooving = false;
var isMoovX = null;
var isMoovY = null;
var showHelpText = true;
var updatePhoto = true;

$(document).ready(function(){
    $('.img-holder img').click(function(){
        if (showHelpText) {
            var dImgPos = $(this).position();
            $('#help-msg').css({ opacity: 1, left: dImgPos.left, top: dImgPos.top - $('#help-msg').outerHeight() }).show();
            setTimeout(function(){ $('#help-msg').animate({opacity: 0}, 1000) }, 7000);
        }
    });
    $('#help-msg').click(function(){$('#help-msg').hide();});
    editableEl = $('#insert-img1');
    $('.img-holder').mousedown(handleDragMouseDown);
    document.onmousemove = handleMouseMove;
    document.onmouseup = handleMouseUp;
    pagingEvetsDriving();
    $('.studio-funcional-btn .rotateleft90btn').click(function(){rotateImage(this, 90);});
    $('.studio-funcional-btn .rotateleft180btn').click(function(){rotateImage(this, 180);});
    $('.studio-funcional-btn .rotateright90btn').click(function(){rotateImage(this, -90);});
    $('.studio-funcional-btn .rotateright180btn').click(function(){rotateImage(this, -180);});
    $('.studio-funcional-btn .leftbtn').mousedown(function(){resizeTimer = true; scrollLeftImage(this, 1, true);})
    $('.studio-funcional-btn .rightbtn').mousedown(function(){resizeTimer = true; scrollRightImage(this, 1, true);})
    $('.studio-funcional-btn .upbtn').mousedown(function(){resizeTimer = true; scrollTopImage(this, 1, true);})
    $('.studio-funcional-btn .downbtn').mousedown(function(){resizeTimer = true; scrollDownImage(this, 1, true);})
    $('.studio-funcional-btn .leftbtn').mouseup(function(){resizeTimer = false;})
    $('.studio-funcional-btn .rightbtn').mouseup(function(){resizeTimer = false;})
    $('.studio-funcional-btn .upbtn').mouseup(function(){resizeTimer = false;})
    $('.studio-funcional-btn .downbtn').mouseup(function(){resizeTimer = false;})
    $('.studio-funcional-btn .plasbtn').mousedown(function(){resizeTimer = true; imageSizerBigger(this);})
    $('.studio-funcional-btn .minusbtn').mousedown(function(){resizeTimer = true; imgSizerSmoller(this);})
    $('.studio-funcional-btn .plasbtn').mouseup(function(){resizeTimer = false;})
    $('.studio-funcional-btn .minusbtn').mouseup(function(){resizeTimer = false;})
    $('.studio-funcional-btn .centerbtn').click(function(){
        if($(this).parents(".functional-btn").length > 0){
            var numEl = $(this).parents(".functional-btn").attr("id").split("_");
            var num = numEl[1];
            editableEl = $("#insert-img" + num);
        }
        if(editableEl != null) moveImageCenter("small");
    })
    $(".studio-funcional-btn .fullbtn").click(function(){
        if($(this).parents(".functional-btn").length > 0){
            var numEl = $(this).parents(".functional-btn").attr("id").split("_");
            var num = numEl[1];
            editableEl = $("#insert-img" + num);
        }
        if(editableEl != null) moveImageCenter("full");
    })
    $(".content").mousedown(handleMoveMouseDown);
    $("#selectTheme").click(function(){
        if($("#imgType").val() == "Content") {
            showBox(this.href);
        }
        this.blur();
        return false;
    });
    $("#add-photo, #upload-helper, .upload-img").click(function(){
        updatePhoto = false;
        showBox($("#add-photo").attr("href") + "?albumId=" + $("#currentAlbumId").val());
        this.blur();
        return false;
    });
    $(".mboxUpload").click(function(){
        updatePhoto = false;
        showBox(this.href + "&albumId=" + $("#currentAlbumId").val());
        this.blur();
        return false;
    });

    $("#imgTypeUsers").click(function(){
        $("#imgType").val("Users");
        $(".imgTypeContent").removeClass("imgTypeContent").addClass("imgTypeUser");
        $("#contentElGroup .selected").hide();
        $("#imgTypeUsers").hide();
        $("#imgTypeContent").show();
        $("#userElGroup .selected").show();
        if($("#currentAlbumId").val() != "none") {
            changePage(1, $("#currentAlbumId").val());
        } else{
            showBox(baseUrl + "/produces/useralbum?height=254&width=352");
        }
    });
    $("#imgTypeContent").click(function(){
        $("#imgType").val("Content");
        $(".imgTypeUser").removeClass("imgTypeUser").addClass("imgTypeContent");
        $("#userElGroup .selected").hide();
        $("#imgTypeContent").hide();
        $("#imgTypeUsers").show();
        $("#contentElGroup .selected").show();
        changePage(1, $("#currentThemeId").val());
    });
});
function handleDragMouseDown(ev){
    dragTarget = $(this);
    ev = fixEvent(ev);
    dragTarget.css({ position: "absolute", opacity: 0.7 });
    dragTarget.css({
        left: ev.pageX - Math.round(dragTarget.outerWidth()/ 2),
        top: ev.pageY - Math.round(dragTarget.outerHeight()/2)
    });
    showHelpText = true;
    isMoovX = ev.pageX;
    isMoovY = ev.pageY;
    return false;
}
function handleMoveMouseDown(ev){
    if($(this).parents(".insert-img").length > 0){
        editableEl = $(this).parents(".insert-img");
    }
    ev = fixEvent(ev);
    isMooving = true;
    isMoovX = ev.pageX;
    isMoovY = ev.pageY;
    $(".content").css({ 'cursor':'move' });
    return false;
}
function handleMouseMove(ev){
    if(isMooving == true){
        ev = fixEvent(ev);
        deltaX = isMoovX - ev.pageX;
        deltaY = isMoovY - ev.pageY;
        isMoovX = ev.pageX;
        isMoovY = ev.pageY;
        if(deltaX > 0){
            scrollRightImage(null, deltaX, false);
        } else{
            scrollLeftImage(null, Math.abs(deltaX), false);
        }
        if(deltaY > 0){
            scrollTopImage(null, deltaY, false);
        } else{
            scrollDownImage(null, Math.abs(deltaY), false);
        }
        return false;
    } else if(dragTarget != null){
        ev = fixEvent(ev);
        deltaX = isMoovX - ev.pageX;
        deltaY = isMoovY - ev.pageY;
        if((Math.abs(deltaX) > 7)||(Math.abs(deltaY) > 7)) { showHelpText = false; }
        $("#help-msg").hide();
        dragTarget.css({
            left: ev.pageX - Math.round(dragTarget.outerWidth()/ 2),
            top: ev.pageY - Math.round(dragTarget.outerHeight()/2)
        });
        $(".insert-img").each(function(){
            var targPos = $(this).position();
            var dataPosition = $("#dragImagesStudia").position();
            if((ev.pageX > (targPos.left + dataPosition.left)) && (ev.pageX < ((targPos.left + dataPosition.left) + $(this).outerWidth())) &&
                (ev.pageY > (targPos.top + dataPosition.top)) && (ev.pageY < ((targPos.top + dataPosition.top) + $(this).outerHeight()))){
                $("#" + $(this).attr("id") + " .printData").css({ opacity: 0.7 });
            } else {
                $("#" + $(this).attr("id") + " .printData").css({ opacity: 1 });
            }
        });
        return false;
    }
}
function handleMouseUp(ev){
    if(isMooving == true) {
        isMooving = false;
    }
    if(dragTarget != null){
        ev = fixEvent(ev);
        $(".insert-img").each(function(){
            var targPos = $(this).position();
            if((ev.pageX > (targPos.left)) && (ev.pageX < ((targPos.left) + $(this).outerWidth()))
                && (ev.pageY > (targPos.top)) && (ev.pageY < ((targPos.top) + $(this).outerHeight()))
            ){
                editableEl = $(this);
                $("#" + $(this).attr("id") + " .printData").css({ opacity: 1 });
                var src = $("#" + dragTarget.attr("id") + " img").attr("src").replace( '.s.' ,'.p.' );
                changePicture(src);
            }
        });
        dragTarget.css({ 'position': "", 'opacity': 1 });
        dragTarget = null;
    }
}
function fixEvent(ev) {
    ev = ev || window.event;
    if ( ev.pageX == null && ev.clientX != null ) {
        var html = document.documentElement;
        var body = document.body;
        ev.pageX = ev.clientX + (html && html.scrollLeft || body && body.scrollLeft || 0) - (html.clientLeft || 0);
        ev.pageY = ev.clientY + (html && html.scrollTop || body && body.scrollTop || 0) - (html.clientTop || 0);
    }
    if (!ev.which && ev.button) {
        ev.which = ev.button & 1 ? 1 : ( ev.button & 2 ? 3 : ( ev.button & 4 ? 2 : 0 ) );
    }
    return ev;
}
function setLoaderImg(){
    if(editableEl != null){
        img = $("#" + editableEl.attr("id") + " .printData")
        img.attr("src", loaderImg);
        img.width("");
        img.height("");
        var plane_width = editableEl.width();
        var plane_height = editableEl.height();
        img.css({
            "margin-left": Math.round((plane_width / 2) - 25),
            "margin-top": Math.round((plane_height / 2) - 25)
            });
    }
}
function changePage(page_number, albumId){
    var param = { page: page_number };
    if (albumId != '0') {
        param.albumId = albumId
    }
    $.getJSON(
        baseUrl + '/produces/image'
        , param
        , function(data) {
            for(var key in data.imagesList){
                $('#' + key + ' img').attr('src', data.imagesList[key].path);
                if(data.imagesList[key].id){
                    $('#' + key + ' img').attr("class", '');
                    if(key == 0){ $("#upload-helper").hide(); }
                }else{
                    $('#' + key + ' img').attr("class", 'hidden');
                    if(key == 0){ $("#upload-helper").show(); }
                }
            }
            $('#pagination').html(data.pagingHtml);
            pagingEvetsDriving();
        }
    );
    return false;
}
function pagingEvetsDriving(){
    $(".paging").click(function (){
        changePage($(this).attr('data-page-number'), $('#currentAlbumId').val());
        return false;
    });
}
function rotateImage(el, degree){
    if($(el).parents(".functional-btn").length > 0){
        var numEl = $(el).parents(".functional-btn").attr("id").split("_");
        var num = numEl[1];
        editableEl = $("#insert-img" + num);
    }
    if(editableEl != null){
        var img_go = $("#" + editableEl.attr("id") + " .printData").attr("src");
        $("#" + editableEl.attr("id") + " .printData").attr("src", "");
        setLoaderImg();
        imgName = img_go.match(/[0-9a-z]{32}\-[0-9a-z]{40}\.[0-9a-z\.]+/g)[0];
        $.getJSON(
            baseUrl + '/user/rotateimage',
            {
                img_go: imgName,
                degree: degree
            },
            function(data) {
                if(data.status == 'success'){
                    changePicture(data.urlname);
                }else{
                    alert(data.status);
                }
            }
        );
    }
}
function changePicture(src) {
    if(src != ''){
        $("#" + editableEl.attr("id") + ' .start-txt').hide();
        var current_el = editableEl.attr("id");
        $("#" + current_el + " .printData").attr("src", "");
        setLoaderImg();
        pic_coll[current_el] = new Image();
        pic_coll[current_el].src = src;
        setTimeout(function(){ chakLoading(); }, 500);
    }
}
function chakLoading(carrent_el){
    if (pic_coll[editableEl.attr("id")].complete == true){
        moveImageCenter("full");
        $(".content").css({ 'cursor':'pointer' });
    }else{
        setTimeout(function(){ chakLoading(); }, 500);
    }
}
function moveImageCenter(type){
    var el = editableEl.attr("id");
    var pic = pic_coll[el];
    var img = $("#" + el + " .printData");
    img.attr("src",  pic.src);
    var plane_width = $("#" + el).width();
    var plane_height = $("#" + el).height();
    var koef_img = 1;
    if(type == "small") {
        koef_img = ((pic.width/plane_width) > (pic.height/plane_height)) ? pic.width/plane_width : pic.height/plane_height;
    } else if(type == "full") {
        koef_img = ((pic.width/plane_width) < (pic.height/plane_height)) ? pic.width/plane_width : pic.height/plane_height;
    }
    var img_width = Math.round( pic.width / koef_img );
    var img_height = Math.round( pic.height / koef_img );
    img.width(img_width);
    img.height(img_height);
    var marge_left = Math.round((plane_width - img_width) / 2);
    var marge_top = Math.round((plane_height - img_height) / 2);
    img.css({
        "margin-left": marge_left,
        "margin-top": marge_top
    });
}
function scrollLeftImage(el, delta, repeat){
    if($(el).parents(".functional-btn").length > 0){
        var numEl = $(el).parents(".functional-btn").attr("id").split("_");
        var num = numEl[1];
        editableEl = $("#insert-img" + num);
    }
    if(((resizeTimer) && (editableEl != null)) || (repeat == false)){
        var formImageWidth = $(editableEl).width();
        var currentWidth = $("#" + editableEl.attr("id") + " .printData").width();
        var mainMarginLeft = parseInt($("#" + editableEl.attr("id") + " .printData").css("margin-left"));
        if ((mainMarginLeft < 0) || (mainMarginLeft < (formImageWidth - currentWidth))){
            mainMarginLeft += delta;
            if ((mainMarginLeft > 0) && (mainMarginLeft > (formImageWidth - currentWidth))){
                mainMarginLeft = ((formImageWidth - currentWidth) > 0) ? formImageWidth - currentWidth : 0;
            }
            $("#" + editableEl.attr("id") + " .printData").css({ "margin-left": mainMarginLeft });
            if(repeat){
                setTimeout(function(){ scrollLeftImage(el, delta, repeat); }, speedZoom);
            }
        }
    }
}
function scrollRightImage(el, delta, repeat){
    if($(el).parents(".functional-btn").length > 0){
        var numEl = $(el).parents(".functional-btn").attr("id").split("_");
        var num = numEl[1];
        editableEl = $("#insert-img" + num);
    }
    if (((resizeTimer) && (editableEl != null)) || (repeat == false)){
        var formImageWidth = $(editableEl).width();
        var currentWidth = $("#" + editableEl.attr("id") + " .printData").width();
        var mainMarginLeft = parseInt($("#" + editableEl.attr("id") + " .printData").css("margin-left"));
        if ((mainMarginLeft > 0) || (mainMarginLeft > (formImageWidth - currentWidth))){
            mainMarginLeft -= delta;
            if ((mainMarginLeft < 0) && (mainMarginLeft < (formImageWidth - currentWidth))){
                mainMarginLeft = ((formImageWidth - currentWidth) < 0) ? formImageWidth - currentWidth : 0;
            }
            $("#" + editableEl.attr("id") + " .printData").css({ "margin-left": mainMarginLeft});
            if(repeat == true){
                setTimeout(function(){ scrollRightImage(el, delta, repeat); }, speedZoom);
            }
        }
    }
}
function scrollTopImage(el, delta, repeat){
    if($(el).parents(".functional-btn").length > 0){
        var numEl = $(el).parents(".functional-btn").attr("id").split("_");
        var num = numEl[1];
        editableEl = $("#insert-img" + num);
    }
    if(((resizeTimer) && (editableEl != null)) || (repeat == false)){
        var formImageHeight = $(editableEl).height();
        var currentHeight = $("#" + editableEl.attr("id") + " .printData").height();
        var mainMarginTop = parseInt($("#" + editableEl.attr("id") + " .printData").css("margin-top"));
        if ((mainMarginTop > 0) || (mainMarginTop > (formImageHeight - currentHeight))){
            mainMarginTop -= delta;
            if ((mainMarginTop < 0) && (mainMarginTop < (formImageHeight - currentHeight))){
                mainMarginTop = ((formImageHeight - currentHeight) < 0) ? formImageHeight - currentHeight : 0;
            }
            $("#" + editableEl.attr("id") + " .printData").css({ "margin-top": mainMarginTop });
            if(repeat == true){
                setTimeout(function(){ scrollTopImage(el, delta, repeat); }, speedZoom);
            }
        }
    }
}
function scrollDownImage(el, delta, repeat){
    if($(el).parents(".functional-btn").length > 0){
        var numEl = $(el).parents(".functional-btn").attr("id").split("_");
        var num = numEl[1];
        editableEl = $("#insert-img" + num);
    }
    if(((resizeTimer) && (editableEl != null)) || (repeat == false)){
        var formImageHeight = $(editableEl).height();
        var currentHeight = $("#" + editableEl.attr("id") + " .printData").height();
        var mainMarginTop = parseInt($("#" + editableEl.attr("id") + " .printData").css("margin-top"));
        if ((mainMarginTop < 0) || (mainMarginTop < (formImageHeight - currentHeight))){
            mainMarginTop += delta;
            if ((mainMarginTop > 0) && (mainMarginTop > (formImageHeight - currentHeight))){
                mainMarginTop = ((formImageHeight - currentHeight) > 0) ? formImageHeight - currentHeight : 0;
            }
            $("#" + editableEl.attr("id") + " .printData").css({ "margin-top": mainMarginTop });
            if(repeat == true){
                setTimeout(function(){ scrollDownImage(el, delta, repeat); }, speedZoom);
            }
        }
    }
}
function imageSizerBigger(el){
    if($(el).parents(".functional-btn").length > 0){
        var numEl = $(el).parents(".functional-btn").attr("id").split("_");
        var num = numEl[1];
        editableEl = $("#insert-img" + num);
    }
    if((resizeTimer) && (editableEl != null)){
        var formImageWidth = $(editableEl).width();
        var formImageHeight = $(editableEl).height();
        var currentWidth = $("#" + editableEl.attr("id") + " .printData").width();
        var currentHeight = $("#" + editableEl.attr("id") + " .printData").height();
        if ((currentWidth < (3 * formImageWidth)) && (currentHeight < (3 * formImageHeight))){
            var el = editableEl.attr("id");
            var pic = pic_coll[el];
            var proportsia = pic.height / pic.width;
            currentWidth += 2;
            currentHeight =  Math.round(currentWidth * proportsia);
            $("#" + editableEl.attr("id") + " .printData").width(currentWidth);
            $("#" + editableEl.attr("id") + " .printData").height(currentHeight);
            var margLeftCen = Math.round((formImageWidth - currentWidth)/2);
            var mainMarginLeft = parseInt($("#" + editableEl.attr("id") + " .printData").css("margin-left"));
            if(isNaN(mainMarginLeft)) mainScrollLeft = 0;
            var  znak= Math.round((margLeftCen - mainMarginLeft) / Math.abs(margLeftCen - mainMarginLeft));
            if(isNaN(znak)) znak = 0;
            var newMergLeft = (Math.abs(margLeftCen - mainMarginLeft) <= (speedZoom/2)) ? margLeftCen : Math.round(mainMarginLeft + znak);
            $("#" + editableEl.attr("id") + " .printData").css({ "margin-left": newMergLeft });
            var margTopCen = Math.round((formImageHeight - currentHeight)/2);
            var mainMarginTop = parseInt($("#" + editableEl.attr("id") + " .printData").css("margin-top"));
            if(isNaN(mainMarginTop)) mainMarginTop = 0;
            var znak = Math.round((margTopCen - mainMarginTop) / Math.abs(margTopCen - mainMarginTop));
            if(isNaN(znak)) znak = 0;
            var newMergTop = (Math.abs(margTopCen - mainMarginTop) <= (speedZoom/2)) ? margTopCen : Math.round(mainMarginTop + znak);
            $("#" + editableEl.attr("id") + " .printData").css({ "margin-top": newMergTop });
            setTimeout(function(){ imageSizerBigger(); }, speedZoom);
        }
    }
}
function imgSizerSmoller(el){
    if($(el).parents(".functional-btn").length > 0){
        var numEl = $(el).parents(".functional-btn").attr("id").split("_");
        var num = numEl[1];
        editableEl = $("#insert-img" + num);
    }
    if((resizeTimer) && (editableEl != null)){
        var formImageWidth = $(editableEl).width();
        var formImageHeight = $(editableEl).height();
        var currentWidth = $("#" + editableEl.attr("id") + " .printData").width();
        var currentHeight = $("#" + editableEl.attr("id") + " .printData").height();
        if (((3 * currentWidth) > formImageWidth) && ((3 * currentHeight) > formImageHeight)){
            var el = editableEl.attr("id");
            var pic = pic_coll[el];
            var proportsia = pic.height / pic.width;
            currentWidth -= 2;
            currentHeight =  Math.round(currentWidth * proportsia);
            $("#" + editableEl.attr("id") + " .printData").width(currentWidth);
            $("#" + editableEl.attr("id") + " .printData").height(currentHeight);
            var margLeftCen = Math.round((formImageWidth - currentWidth)/2);
            var mainMarginLeft = parseInt($("#" + editableEl.attr("id") + " .printData").css("margin-left"));
            if(isNaN(mainMarginLeft)) mainScrollLeft = 0;
            var  znak= Math.round((margLeftCen - mainMarginLeft) / Math.abs(margLeftCen - mainMarginLeft));
            if(isNaN(znak)) znak = 0;
            var newMergLeft = (Math.abs(margLeftCen - mainMarginLeft) <= (speedZoom/2)) ? margLeftCen : Math.round(mainMarginLeft + znak);
            $("#" + editableEl.attr("id") + " .printData").css({ "margin-left": newMergLeft });
            var margTopCen = Math.round((formImageHeight - currentHeight)/2);
            var mainMarginTop = parseInt($("#" + editableEl.attr("id") + " .printData").css("margin-top"));
            if(isNaN(mainMarginTop)) mainMarginTop = 0;
            var znak = Math.round((margTopCen - mainMarginTop) / Math.abs(margTopCen - mainMarginTop));
            if(isNaN(znak)) znak = 0;
            var newMergTop = (Math.abs(margTopCen - mainMarginTop) <= (speedZoom/2)) ? margTopCen : Math.round(mainMarginTop + znak);
            $("#" + editableEl.attr("id") + " .printData").css({ "margin-top": newMergTop });
            setTimeout(function(){ imgSizerSmoller(el); }, speedZoom);
        }
    }
}

function changeTheme(theme){
    $("#currentThemeId").val(theme);
    changePage(1, theme);
    $('#selectedCurrentTheme').html($('#theme'+theme).html());
    mboxRemove();
    return false;
}

function changeAlbum(album){
    changePage(1, album);
    if($('#album'+album).html()) {
        $('#selectedCurrentAlbum').html($('#album'+album).html());
        $("#currentAlbumId").val(album);
    } else if (! $("#currentAlbumId").val()) {
        $('#selectedCurrentAlbum').html(albumName);
        $("#currentAlbumId").val("0");
    }
    mboxRemove();
    return false;
}

function updateFormPlaseHolder(imgName){
    if(updatePhoto) {
        changePicture(imgName);
        if($("#imgType").val() != "Users"){
            $("#imgType").val("Users");
            $(".imgTypeContent").removeClass("imgTypeContent").addClass("imgTypeUser");
            $("#contentElGroup .selected").hide();
            $("#imgTypeUsers").hide();
            $("#imgTypeContent").show();
            $("#userElGroup .selected").show();
            if($("#currentAlbumId").val() != "none") {
                changePage(1, $("#currentAlbumId").val());
            } else{
                showBox(baseUrl + "/produces/useralbum");
            }
        }
    } else {
        updatePhoto = true;
    }
    mboxRemove();
    changeAlbum($("#currentAlbumId").val());
}

function buildImgXml(type, imgEl){
    if(typeof(imgEl) == 'object'){
        var xml = '<img type="' + type + '">'
            + '<url>' + imgEl.src + '</url>'
            + '<width>' + parseInt(imgEl.width) + '</width>'
            + '<height>' + parseInt(imgEl.height) + '</height>'
            + '<top>' + parseInt(imgEl.top) + '</top>'
            + '<left>' + parseInt(imgEl.left) + '</left>'
            + '</img>';
    } else {
        var domElementId = '#insert-img' + imgEl + ' .printData';
        var xml = '<img type="' + type + '">'
            + '<url>' + $(domElementId).attr("src") + '</url>'
            + '<width>' + parseInt($(domElementId).css("width")) + '</width>'
            + '<height>' + parseInt($(domElementId).css("height")) + '</height>'
            + '<top>' + parseInt($(domElementId).css("margin-top")) + '</top>'
            + '<left>' + parseInt($(domElementId).css("margin-left")) + '</left>'
            + '</img>';
    }
    return xml
}

function buildImgObj(type, imgElNumber){
    return {
        src: $("#insert-img" + imgElNumber + " .printData").attr("src"),
        width: parseInt($("#insert-img" + imgElNumber + " .printData").css("width")),
        height: parseInt($("#insert-img" + imgElNumber + " .printData").css("height")),
        top: parseInt($("#insert-img" + imgElNumber + " .printData").css("margin-top")),
        left: parseInt($("#insert-img" + imgElNumber + " .printData").css("margin-left"))
    }
}

function setImgFromObj(imgElNumber, obj){
    $("#insert-img" + imgElNumber + " .printData").attr("src", obj.src)
    $("#insert-img" + imgElNumber + " .printData").css("width", obj.width);
    $("#insert-img" + imgElNumber + " .printData").css("height", obj.height);
    $("#insert-img" + imgElNumber + " .printData").css("margin-top", obj.top);
    $("#insert-img" + imgElNumber + " .printData").css("margin-left", obj.left);
}
