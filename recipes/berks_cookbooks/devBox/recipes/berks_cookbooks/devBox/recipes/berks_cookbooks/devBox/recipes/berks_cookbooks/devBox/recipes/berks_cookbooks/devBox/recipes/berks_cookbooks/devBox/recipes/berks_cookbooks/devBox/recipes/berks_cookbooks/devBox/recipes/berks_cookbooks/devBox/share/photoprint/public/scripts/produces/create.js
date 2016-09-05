function saveItem(xmlData){
    var contentXml = '<?xml version="1.0" encoding="UTF-8"?>';
    contentXml += '<data>';
    contentXml += '<item>' + $('#item').val() + '</item>';
    contentXml += '<group>' + $('#group').val() + '</group>';
    contentXml += '<template>' + $('#template').val() + '</template>';
    contentXml += xmlData;
    contentXml += '</data>';
    var itemId = ($('#itemId').length) ? $('#itemId').val() : 'none';
    $.getJSON(
        baseUrl + '/user/addtobusket'
        , {
            id: itemId
            , group: $('#group').val()
            , xml: contentXml
        }
        , function(data) {
            if(itemId == 'none'){
                $('#basket-items-count').text(parseInt($('#basket-items-count').text()) + 1);
                showBox(baseUrl + '/user/addtobusketwnd');
            } else {
                document.location.href = baseUrl + '/user/basket';
            }
        }
    );
}