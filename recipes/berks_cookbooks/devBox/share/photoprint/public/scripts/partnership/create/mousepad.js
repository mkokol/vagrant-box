$(document).ready(function(){
    $(".studio-funcional-btn .basketbtn").click(function(){
        var img_go = $("#insert-img1 .printData").attr("src").split("/theme/produces/");
        if(!isDefined(img_go[1])) {
            xmlData = buildImgXml("main", 1);
            saveItem(xmlData);
        } else {
            alert(select_photo_first);
        }
    })
})