/**
 * micko
 */
$(document).ready(function(){
    $(".price .editBtn").click(function(){
        $(this).hide();
        var value = $(this).parents("tr").find(".elPrice").html();
        $(this).parents("tr").find(".elPrice").html("<input class=\"updatePrice\" type=\"text\"/>");
        $(this).parent("div").find(".saveBtn").show();
        $(this).parents("tr").find(".updatePrice").val(value);
        $(this).parents("tr").find(".updatePrice").focus();
    });
    $(".price .saveBtn").click(function(){
        var value = $(this).parents("tr").find(".updatePrice").val();
        var id = $(this).parents("tr").find(".elPrice").attr("data-item-id");
        var templateId = $(this).parents("tr").find(".elPrice").attr("data-template-id");
        $.getJSON(
            baseUrl + "/admin/setprice?v=" + baseVersion,
            {itemId: id, templateId:templateId, itemPrice: value},
            function(data){}
        );
        $(this).hide();
        $(this).parent("div").find(".editBtn").show();
        $(this).parents("tr").find(".elPrice").html(value);
    });
    $(".published .editBtn").click(function(){
        $(this).hide();
        var value = $(this).parents("tr").find(".elPublished").html();
        $(this).parents("tr").find(".elPublished").html("<select class=\"updatePublished\"><option value=\"0\">0</option><option value=\"1\">1</option></select>");
        $(this).parent("div").find(".saveBtn").show();
        $(this).parents("tr").find(".updatePublished").val(value);
        $(this).parents("tr").find(".updatePublished").focus();
    });
    $(".published .saveBtn").click(function(){
        var value = $(this).parents("tr").find(".updatePublished").val();
        var id = $(this).parents("tr").find(".elPublished").attr("data-item-id");
        $.getJSON(
            baseUrl + "/admin/setpublished?v=" + baseVersion,
            {itemId: id, itemPublished: value},
            function(data){}
        );
        $(this).hide();
        $(this).parent("div").find(".editBtn").show();
        $(this).parents("tr").find(".elPublished").html(value);
    });
});
