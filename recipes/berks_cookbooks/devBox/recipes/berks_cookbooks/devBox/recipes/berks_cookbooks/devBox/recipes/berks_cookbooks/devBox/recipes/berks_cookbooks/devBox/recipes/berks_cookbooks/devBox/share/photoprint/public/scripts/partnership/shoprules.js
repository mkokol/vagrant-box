$(document).ready(function(){
    $("#accept-rules").click(function(){
        if($('#agreed-with-rules').is(':checked')){
            $.ajax({
                type: "POST",
                url: baseUrl + "/partnership/acceptrules",
                data: {},
                success: function(data) {
                    document.location.href = baseUrl + "/partnership";
                }
            });
        } else {
            alert("rules_not_accepted");
        }
    });
});
