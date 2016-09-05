$(document).ready(function(){
    $(".studio-funcional-btn .basketbtn").click(function(){
        var mainImg = buildImgXml("main", 1);
        if(mainImg != "") {
            var xmlData = mainImg;
            xmlData += '<content><text></text></content>';
            saveItem(xmlData);
        } else {
            alert(select_photo_first);
        }
    });
});