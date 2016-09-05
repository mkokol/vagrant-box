$(document).ready(function(){
    $("#login-btn").click(function() {
        var loginEmail = $("#login_email").val();
        var loginPassword = $("#login_password").val();
        if((loginEmail != '') && (loginPassword != '')){
            $('.popup-wnd-content').removeClass('popup-wnd-content-errors');
            $('.popup-wnd-errors').html('');
            $.ajax({
                type: "POST",
                url: baseUrl + "/user/login",
                data: {
                    email: loginEmail,
                    password: loginPassword
                },
                success: function(data) {
                    if (data.status == 'success') {
                        mboxRemove();
                        location.href = baseUrl + '/';
                    } else {
                        $("#login_password").val('');
                        $('.popup-wnd-content').addClass('popup-wnd-content-errors');
                        $('.popup-wnd-errors').html(incorretInputData);
                    }
                }
            });
        }else{
            $('.popup-wnd-content').addClass('popup-wnd-content-errors');
            $('.popup-wnd-errors').html(incorretMissedData);
        }
    });

    $("#login_email").keypress(function goToPass(event){
        if(event.keyCode == 13)
            var focuseToPass = setTimeout('$("#login_password").focus()', 100);
        return true;
    });

    $("#login_password").keypress(function goToLogIn(event){
        if(event.keyCode == 13)
            $("#login-btn").click();
        return true;
    });
});

