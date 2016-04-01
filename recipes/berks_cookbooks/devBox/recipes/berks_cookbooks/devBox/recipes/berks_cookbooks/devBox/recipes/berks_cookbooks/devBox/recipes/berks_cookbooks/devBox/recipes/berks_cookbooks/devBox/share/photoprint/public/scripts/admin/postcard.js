var marks = new Array ("mark1", "mark2");
function mark(num) {
    for ( i = 1; i < 4; i++ ){
        $("#bookmark" + i).attr("src", baseUrl + "/public/theme/produces/postcard/top_hiden.png");
        $("#mark" + i).css({"border-bottom": "1px solid #839e4d", "background": "#d6eda7"});
    }
    $("#bookmark" + num).attr("src", baseUrl + "/public/theme/produces/postcard/top.png");
    $("#mark" + num).css({"border-bottom": 0, "background": "#f2ffd7"});
    $(".tabs").hide();
    $("#vTab" + num).show();
}