var xmlDataStructure = null;
function saveItem(xmlData){
    xmlDataStructure = xmlData
    showBox(baseUrl + '/wl/addtheme/code/' + $('#dragImagesStudia').attr('data-wl-code') + '?group=' + $('#group').val());
}

function saveProductItem(){
    var contentXml = '<?xml version="1.0" encoding="UTF-8"?>';
    contentXml += '<data>';
    contentXml += '<item>' + $('#item').val() + '</item>';
    contentXml += '<group>' + $('#group').val() + '</group>';
    contentXml += '<template>' + $('#template').val() + '</template>';
    contentXml += xmlDataStructure;
    contentXml += '</data>';
    var itemId = ($('#itemId').length) ? $('#itemId').val() : undefined;
    $.getJSON(
        baseUrl + '/wl/saveproduct/code/' + $('#dragImagesStudia').attr('data-wl-code'),
        {
            id: itemId,
            group: $("#group").val(),
            themeId: $('#themeId').val(),
            newTheme: $("#new-theme").val(),
            templateId: $('#template').val(),
            xml: contentXml
        },
        function(data) {
            document.location.href = baseUrl + '/wl/products/code/' + $('#dragImagesStudia').attr('data-wl-code');
        }
    );
}