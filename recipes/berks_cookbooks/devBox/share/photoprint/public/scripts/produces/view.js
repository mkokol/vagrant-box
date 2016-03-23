$(document).ready(function () {
    if (isDefined($('#tshirt-sizes'))) {
        $('#tshirt-sizes span').click(function () {
            $('#tshirt-sizes span').removeClass('selected');
            $(this).addClass('selected');
            $('#add-to-basket #size').val($(this).text());
        });
    }

    if (isDefined($('#tshirt-color'))) {
        $('#tshirt-color span').click(function () {
            $('#tshirt-color span').removeClass('selected');
            $(this).addClass('selected');
            $('.view img').attr({'src': $(this).attr('data-img')});
        });
    }

    $('#add-to-basket').submit(function () {
        $('#add-to-basket #color').val($('#tshirt-color span.selected').attr('data-color'));

        if ($('#tshirt-sizes').length && !$('#tshirt-sizes span.selected').length) {
            showBox(baseUrl + '/index/message?message=please_select_tshirt_size');
            return false;
        }

        $(this).ajaxSubmit(function (data) {
            if (data.status = 'success') {
                showBox(baseUrl + '/user/gotobusket');
            }
        });

        return false;
    });

    $(".h-nav li").bind('touchstart click', function(e){
        e.preventDefault();
        var that = $(this);
        if(!$(".ajax-overlay").length) {
            $("#" + $(".navigation-tabs").find(".selected").attr("data-content-el") + "-product").append("<div class=\"ajax-overlay\"></div>");
            if(!$("#" + that.attr("data-content-el") + "-product").length) {
                $.ajax({
                    type: "POST",
                    url: baseUrl + "/produces/" + $(this).attr("data-content-el"),
                    data: {},
                    success: function(data) {
                        hideAllTabs(that);
                        $("#similar-product").after(data.html);
                        $(".ajax-overlay").remove();
                    }
                });
            } else {
                hideAllTabs(that);
                $("#" + $(this).attr("data-content-el") + "-product").show();
                $(".ajax-overlay").remove();
            }
        }

        return false;
    });
});

function hideAllTabs(tabEl){
    $("#similar-product").hide();
    $("#warranty-product").hide();
    $(".h-nav li").removeClass("selected");
    tabEl.addClass("selected");
}
