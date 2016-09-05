// -- to do Elizabeth Loaiza
var colorImgObj = {
    currentSide: 'front',
    emptyImg: basePath + '/public/theme/produces/none.png',
    defaultImgFront: '',
    defaultImgBack: '',
    colors: {}
};
$(document).ready(function () {
    if ($('#template').val() == '') {
        $('#template').val('1');
    } else {
        if(parseInt($('#template').val()) == 2){
            colorImgObj.currentSide = 'back';
        }
    }
    if ($('#tshirt-size').length == 0) {
        var imgSrc1 = $('#insert-img1').find('.printData').attr('src');
        if(imgSrc1 != colorImgObj.emptyImg){
            colorImgObj.defaultImgFront = buildImgObj('front', 1);
        }
        var imgSrc2 = $('#insert-img2').find('.printData').attr('src');
        if(imgSrc2 != colorImgObj.emptyImg){
            colorImgObj.defaultImgBack = buildImgObj('back', 2);
        }

    }
    $('#tshirt-color span').each(function(){
        var newCollor = $(this).attr('data-color');
        var newCollorInfo = ($(this).attr('data-img-config') != '') ? $.parseJSON($(this).attr('data-img-config')) : null;
        colorImgObj.colors[newCollor] = newCollorInfo;
    });
    $('.product-template').click(function () {
        $('#tshirt-front, #tshirt-back').removeClass('back').addClass('front');
        $('.tshirt-sides').removeClass('tshirt-template-1 tshirt-template-2 tshirt-template-3').addClass('tshirt-template-' + $(this).val());
        $('#template').val($(this).val());
    });
    $('.studio-funcional-btn .basketbtn').click(function () {
        initDefaultImg();
        var xmlData =
            ((colorImgObj.defaultImgFront != '') ? buildImgXml('front', colorImgObj.defaultImgFront) : '')
            + ((colorImgObj.defaultImgBack != '') ? buildImgXml('back', colorImgObj.defaultImgBack) : '');
        for (var color in colorImgObj.colors) {
            if(colorImgObj.colors[color] != null){
                xmlData += '<' + color + '>'
                    + ((isDefined(colorImgObj.colors[color].front)) ? buildImgXml('front', colorImgObj.colors[color].front) : '')
                    + ((isDefined(colorImgObj.colors[color].back)) ? buildImgXml('back', colorImgObj.colors[color].back) : '')
                    + '</' + color + '>';
            }
        }
        saveItem(xmlData);
    });
    $('#tshirt-color span').click(function () {
        initDefaultImg();
        $('.tshirt-sides').css({'background-color': $(this).css('background-color')});
        $('#tshirt-color').attr({'data-selected-color': $(this).attr('data-color')});
        showDefaultImg()
    });
});

function showSide(side) {
    initDefaultImg();
    colorImgObj.currentSide = side;
    $('#tshirt-front, #tshirt-back').removeClass((side == 'front') ? 'back' : 'front').addClass(side);
    editableEl = (side == 'back') ? $('#insert-img1') : $('#insert-img2');
}

function initDefaultImg() {
    var currentImgSrc = editableEl.find('.printData').attr('src');

    if(currentImgSrc != colorImgObj.emptyImg && colorImgObj.currentSide == 'front'
        && (colorImgObj.defaultImgFront == '' || colorImgObj.defaultImgFront.src == currentImgSrc)){
        colorImgObj.defaultImgFront = buildImgObj('front', 1);
    }
    if(currentImgSrc != colorImgObj.emptyImg && colorImgObj.currentSide == 'back'
        && (colorImgObj.defaultImgBack == '' || colorImgObj.defaultImgBack.src == currentImgSrc)){
        colorImgObj.defaultImgBack = buildImgObj('back', 2);
    }

    if(colorImgObj.defaultImgFront != '' && colorImgObj.currentSide == 'front' && currentImgSrc != colorImgObj.defaultImgFront.src){
        var currentColor =  $('#tshirt-color span.selected').attr('data-color');
        if(colorImgObj.colors[currentColor] == null){ colorImgObj.colors[currentColor] = {}; }
        colorImgObj.colors[currentColor].front = buildImgObj('front', 1);
    }
    if(colorImgObj.defaultImgBack != '' && colorImgObj.currentSide == 'back' && currentImgSrc != colorImgObj.defaultImgBack.src){
        var currentColor =  $('#tshirt-color span.selected').attr('data-color');
        if(colorImgObj.colors[currentColor] == null){ colorImgObj.colors[currentColor] = {}; }
        colorImgObj.colors[currentColor].back = buildImgObj('back', 2);
    }
}

function showDefaultImg() {
    var currentColor = $('#tshirt-color span.selected').attr('data-color');

    if(colorImgObj.colors[currentColor] != null && isDefined(colorImgObj.colors[currentColor].front)){
        setImgFromObj(1, colorImgObj.colors[currentColor].front);
    } else {
        if(colorImgObj.defaultImgFront != ''){
            setImgFromObj(1, colorImgObj.defaultImgFront);
        }
    }

    if(colorImgObj.colors[currentColor] != null && isDefined(colorImgObj.colors[currentColor].back)){
        setImgFromObj(2, colorImgObj.colors[currentColor].back);
    } else {
        if(colorImgObj.defaultImgBack != ''){
            setImgFromObj(2, colorImgObj.defaultImgBack);
        }
    }
}
