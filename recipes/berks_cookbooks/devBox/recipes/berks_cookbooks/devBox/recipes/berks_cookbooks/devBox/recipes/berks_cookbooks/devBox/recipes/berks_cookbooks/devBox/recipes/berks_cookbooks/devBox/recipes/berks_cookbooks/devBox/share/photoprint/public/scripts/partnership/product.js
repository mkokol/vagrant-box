var xmlDataStructure = null;
function saveItem(xmlData){
    xmlDataStructure = xmlData;
    showBox(baseUrl + '/index/message?message=your_product_is_saved_in_nearest_future_we_publish_it_and_notify_you');
    saveProductItem();
    //showBox(baseUrl + '/partnership/addtheme?group='+$('#group').val());
}

function saveProductItem(){
    var contentXml = '<?xml version="1.0" encoding="UTF-8"?>';
    contentXml += '<data>';
    contentXml += '<item>' + $("#item").val() + '</item>';
    contentXml += '<group>' + $("#group").val() + '</group>';
    contentXml += "<template>" + $("#template").val() + "</template>";
    contentXml += xmlDataStructure;
    contentXml += '</data>';
    var itemId = ($("#itemId").length) ? $("#itemId").val() : undefined;

    $.getJSON(
        baseUrl + '/partnership/saveproduct',
        {
            id: itemId,
            group: $("#group").val(),
            themeId: $('#theme-id').val(),
            newTheme: $("#new-theme").val(),
            subThemeId: $('#sub-theme-id').val(),
            newSubTheme: $("#new-sub-theme").val(),
            xml: contentXml
        },
        function(data) {
            document.location.href = baseUrl + "/partnership?type=public";
        }
    );
}
