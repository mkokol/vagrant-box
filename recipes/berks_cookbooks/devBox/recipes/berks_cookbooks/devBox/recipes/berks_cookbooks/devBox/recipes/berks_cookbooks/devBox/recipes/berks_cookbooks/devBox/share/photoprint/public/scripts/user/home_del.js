$(document).ready(function(){
    $(".accordion h3:#activeItemHover").addClass("active");
    $(".accordion .accElement:#activeItem").show();
    $(".accordion .accElement:not(#activeItem)").hide();

    $("#makeOrder").submit(function(){
        var itemCount = $("#item_count").val();
        var hasItem = false;
        for(var i = 1; i < itemCount; i++) {
            if($("#basketItem" + i).attr("checked")){
                hasItem = true;
                break;
            }
        }
        if(!hasItem){
            alert(select_item_first);
        }
        return hasItem;
    });

    $("#userImages").load(baseUrl + "/user/albums?v=" + baseVersion);
    $("#deleteImageForm").click(function(){
        var idListForDelete = "";
        var chImgName = $("#userImages").find(".chImgName:checked").each(function(){
            var elId = ($(this).attr("id").split("_"))[1];
            idListForDelete += (idListForDelete != "") ? ";"+elId : elId;
        });
        if(idListForDelete != "") {
            $.getJSON(
                baseUrl + "/user/deleteimages?v=" + baseVersion,
                {
                    listId: idListForDelete
                },
                function(data) {
                    var albumId = ($("#currentAlbumId").length > 0) ? $("#currentAlbumId").val() : 0;
                    $("#userImages").load(baseUrl + "/user/images?albumId=" + albumId + "&v=" + baseVersion);
                }
                );
        }
    });
    $("#deleteAlbumForm").click(function(){
        var idListForDelete = "";
        var chImgName = $("#userImages").find(".chImgName:checked").each(function(){
            var elId = ($(this).attr("id").split("_"))[1];
            idListForDelete += (idListForDelete != "") ? ";"+elId : elId;
        });
        if(idListForDelete != "") {
            $.getJSON(
                baseUrl + "/user/deletealbums?v=" + baseVersion,
                {
                    listId: idListForDelete
                },
                function(data) {
                    $("#userImages").load(baseUrl + "/user/albums?v=" + baseVersion);
                }
                );
        }
    });

    $("#addAlbumForm").click(function(){
        var createAlbum = {
            ok : function(  ) {
                var title = $("#textDialog").val();
                if(title != "") {
                    $.getJSON(
                        baseUrl + "/user/createalbum?v=" + baseVersion,
                        {
                            title: title
                        },
                        function(data) {
                            mboxRemove();
                            $("#userImages").load(baseUrl + "/user/albums?v=" + baseVersion);
                        }
                        );
                }
            }
        };
        var dialogContent = "<h3 style=\"padding-top:7px; padding-bottom:7px;\">" + album_dialog + "</h3>" +
        "<div style=\"padding-bottom:7px;\"><label>" + album_name + ":</label> <input type=\"text\" id=\"textDialog\" value=\"\"></div>" +
        "<input type=\"button\" id=\"submitDialog\" value=\"" + create_album_btn + "\">" +
        "<input type=\"button\" id=\"cancelDialog\" value=\"" + cancel_album_btn + "\">";
        showDialogBox(dialogContent, createAlbum, 500, 100);
    })

    $(".accordion h3").click(function(){
        if(! $(this).hasClass("active")) {
            $(this).next(".accElement").slideToggle("slow")
            .siblings(".accElement:visible").slideUp("slow");
            $(this).toggleClass("active");
            $(this).siblings("h3").removeClass("active");
        }
    });
    $(".tablesorter").tablesorter();
    $(".deleteItem").click(function(){
        var id = $(this).attr("data-item-id");
        $(this).parent("td").parent("tr").remove();
        $.getJSON(
            baseUrl + "/user/deletebusketitem?v=" + baseVersion,
            {
                itemId: id
            },
            function(data) {
                $("#basket-items-count").text(parseInt($("#basket-items-count").text()) - 1);
            }
        );
    });
    $(".editItem").click(function(){
        var id = $(this).attr("data-item-id");
        var produce = $(this).attr("produce");
        window.location = baseUrl + "/produces/create/id/" + id;
    });
    $(".editBtn").click(function(){
        $(this).hide();
        var value = $(this).parent("div").parent("div").find(".elCountInBasket").html();
        $(this).parents("tr").find(".elCountInBasket").html("<input class=\"updateCount\" type=\"text\"/>");
        $(this).parent("div").find(".saveBtn").show();
        $(this).parents("tr").find(".updateCount").val(value);
        $(this).parents("tr").find(".updateCount").focus();
    });
    $(".saveBtn").click(function(){
        var value = $(this).parent("div").parent("div").find(".updateCount").val();
        var id = $(this).parent("div").parent("div").find(".elCountInBasket").attr("data-item-id");
        $.getJSON(
            baseUrl + "/user/changeitemcount?v=" + baseVersion,
            {
                itemId: id,
                itemcount: value
            },
            function(data){}
            );
        $(this).hide();
        $(this).parent("div").find(".editBtn").show();
        var value = $(this).parent("div").parent("div").find(".updateCount").val();
        $(this).parent("div").parent("div").find(".elCountInBasket").html(value);

        var costsVal = $(this).parent("div").parent("div").find(".elCountInBasket").attr("costsVal");
        var new_price = Math.round(costsVal * value * 100)/100;
        $(this).parents("tr").find("span").html(new_price);
    });
    $("#uploadImageForm").click(function(){
        var albumId = ($("#currentAlbumId").length > 0) ? $("#currentAlbumId").val() : 0;
        var url = baseUrl + "/user/uploadimages?height=203&width=850&albumId=" + albumId + "&v=" + baseVersion;
        showBox(url);
    });
});

function updateFormPlaseHolder(imgName) {
    mboxRemove();
    var albumId = ($("#currentAlbumId").length > 0) ? $("#currentAlbumId").val() : 0;
    $("#userImages").load(baseUrl + "/user/images?albumId=" + albumId + "&v=" + baseVersion);
}

function viewAlbum (albumId) {
    $("#userImages").load(baseUrl + "/user/images?albumId=" + albumId + "&v=" + baseVersion);
    $("#deleteAlbumForm").hide();
    $("#addAlbumForm").hide();
    $("#deleteImageForm").show();
    $("#uploadImageForm").show();
}
function viewAlbums () {
    $("#userImages").load(baseUrl + "/user/albums?v=" + baseVersion);
    $("#deleteAlbumForm").show();
    $("#addAlbumForm").show();
    $("#deleteImageForm").hide();
    $("#uploadImageForm").hide();
}