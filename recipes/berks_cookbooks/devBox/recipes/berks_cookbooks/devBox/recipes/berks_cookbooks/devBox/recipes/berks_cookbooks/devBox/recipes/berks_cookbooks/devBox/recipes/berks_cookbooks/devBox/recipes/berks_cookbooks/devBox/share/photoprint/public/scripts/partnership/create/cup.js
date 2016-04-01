var prevEditableEl = null;
$(document).ready(function(){
    if($("#template").val() == ''){
        $("#template").val('4');
    }
    prevEditableEl = $("#insert-img2");
    $("#cup-long_image").click(function(){
        $('.constructor').removeClass('cup-template-4 template-5');
        $('.constructor').addClass('template-' + $(this).attr('data-template-id'));
        $("#template").val($(this).attr('data-template-id'));
        editableEl = $("#insert-img1");
    });
    $("#cup-double_image").click(function(){
        $('.constructor').removeClass('template-5 template-4');
        $('.constructor').addClass('template-' + $(this).attr('data-template-id'));
        $("#template").val($(this).attr('data-template-id'));
        editableEl = prevEditableEl;
    });
    $(".studio-funcional-btn .basketbtn").click(function(){
        var xmlData = "";
        var addToBasket = true;
        if($("#template").val() == '4'){
            var img_go = $("#insert-img1 .printData").attr("src").split("/theme/produces/");
            if(!isDefined(img_go[1])) {
                xmlData += buildImgXml("main", 1);
            } else {
                addToBasket = false;
            }
        } else if($("#template").val() == '5'){
            var leftImg = buildImgXml("left", 2);
            var rightImg = buildImgXml("right", 3);
            if((leftImg != "") || (rightImg != "")) {
                xmlData = leftImg + rightImg;
            } else {
                addToBasket = false;
            }
        }
        if(addToBasket) {
            saveItem(xmlData);
        } else {
            alert(select_photo_first);
        }
    })
});

function showSide(side) {
    if(side == 'left') {
        $(".double .right").hide();
        $(".double .left").show();
        prevEditableEl = editableEl = $("#insert-img2");
    }
    if(side == 'right') {
        $(".double .left").hide();
        $(".double .right").show();
        prevEditableEl = editableEl = $("#insert-img3");
    }
}
