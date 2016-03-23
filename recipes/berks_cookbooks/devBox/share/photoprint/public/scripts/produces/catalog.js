$(document).ready(function(){
    var page = 1;

    $('#load-more-product').click(function(){
        page++;
        if($('#products').data('page-count') == page) {
            $('#load-more').hide();
        }
        $.ajax({
            type: "GET",
            url: baseUrl + "/produces/catalog-products",
            data: {
                item: $('#catalog').data('item'),
                themeId: $('#categories').data('current'),
                tagId: ($('#tags').length) ? $('#tags').data('current') : '',
                page: page
            },
            success: function(data) {
                $('#products').append(data);
                handleProductPreview();
            }
        });
    });
});
