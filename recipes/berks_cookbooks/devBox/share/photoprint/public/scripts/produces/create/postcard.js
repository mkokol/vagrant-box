var marks = new Array ("mark1", "mark2", "mark3", "mark4");
var last = 0;
var mainImg = '';
var innerImg = '';
$(document).ready(function(){
    $(".studio-funcional-btn .basketbtn").click(function(){
        getImageDataIfExist();
        if(mainImg != "") {
            var xmlData = mainImg;
            xmlData += innerImg;
            xmlData += '<content>';
            xmlData += '<text>' + htmlEntities($("#postcardText").html()) + '</text>';
            xmlData += '</content>';
            saveItem(xmlData);
        } else {
            alert(select_photo_first);
        }
    })
    $(".studio-funcional-btn .editbtn, #postcardTextBox").click(function(){
        showTextEditorBox('text-editor', 433, 325);
    });
});
function htmlEntities(texto){
    //by Micox - elmicoxcodes.blogspot.com - www.ievolutionweb.com
    var i, carac, letra, novo = '';
    for(i=0;i<texto.length;i++){
        carac = texto[i].charCodeAt(0);
        if( (carac > 47 && carac < 58) || (carac > 62 && carac < 127) ){
            //se for numero ou letra normal
            novo += texto[i];
        }else{
            novo += "&#" + carac + ";";
        }
    }
    return novo;
}
function mark(num) {
    getImageDataIfExist();
    editableEl = $("#insert-img" + num);
    for ( i = 1; i < 4; i++ ){
        $("#bookmark" + i).attr("src", baseUrl + "/public/theme/produces/postcard/top_hiden.png");
        $("#mark" + i).css({"border-bottom": "1px solid #839e4d", "background": "#d6eda7"});
    }
    $("#bookmark" + num).attr("src", baseUrl + "/public/theme/produces/postcard/top.png");
    $("#mark" + num).css({"border-bottom": 0, "background": "#f2ffd7"});
    $(".tabs").hide();
    var side = (num == 1) ? 'front' : ((num == 2) ? 'middle' : 'back');
    $(".postcard-tab-" + side).show();
    getImageDataIfExist();
}
function getImageDataIfExist() {
    if($("#insert-img1 .printData").width() > 10){mainImg = buildImgXml("main", 1);}
    if($("#insert-img2 .printData").width() > 10){innerImg = buildImgXml("inner", 2);}
}