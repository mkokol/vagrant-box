$(document).ready(function () {
    if ($('#template').val() == '') {
        $('#template').val('1');
    }
    $('.product-template').click(function () {
        $('.constructor')
            .removeClass('template-1 template-2 template-3')
            .addClass('template-' + $(this).val());
        $('#template').val($(this).val());
    });
    $('.studio-funcional-btn .basketbtn').click(function () {
        var xmlData = '';
        if(isDefined($('#tshirt-sizes'))){
            xmlData += '<size>' + $('#tshirt-sizes span.selected').text().trim() + '</size>'
        }
        xmlData += '<color>' + $('#tshirt-color span.selected').attr('data-color') + '</color>';
        switch (parseInt($('#template').val())) {
            case 1: xmlData += buildImgXml('front', 1); break;
            case 2: xmlData += buildImgXml('back', 2); break;
            case 3: xmlData += buildImgXml('front', 1) + buildImgXml('back', 2); break;
        }
        if ($('#tshirt-sizes').length == 0 || ($('#tshirt-sizes span.selected').text().trim() != '')) {
            saveItem(xmlData);
        } else {
            alert(select_size_first);
        }
    });
    $('#tshirt-color span').click(function () {
        $('#tshirt-color span').removeClass('selected');
        $('.front .content, .back .content').removeClass('white blue green red black');
        $('.front .content, .back .content').addClass($(this).attr('data-color'));
        $(this).addClass('selected');
    });
    $('#tshirt-sizes span').click(function () {
        $('#tshirt-sizes span').removeClass('selected');
        $(this).addClass('selected');
    });
});

function showSide(side) {
    $('.front, .back').removeClass('show');
    $('.' + side).addClass('show');
    editableEl = (side == 'back') ? $('#insert-img1') : $('#insert-img2');
}
