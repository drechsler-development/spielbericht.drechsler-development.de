let Login = function () {

    let errorMessage = $("#errorMessage");

    let Init = function () {

        $("body").on('click', '#btnLogin', function () {

            let emailaddress = $("#emailaddress").val();
            let password = $("#password").val();
            let rememberPasswordCheck = $("#rememberPasswordCheck").is(":checked");

            $.ajax({
                url: "/Login",
                type: "POST",
                data: {
                    emailaddress: emailaddress,
                    password: password,
                    rememberPasswordCheck: rememberPasswordCheck
                },
                dataType: "json",
                success: function (response) {

                    let error = response.error

                    if (error === "") {
                        console.log("Login successful");
                        window.location.href = "/";
                    } else {

                        errorMessage.html(error);
                        errorMessage.show();

                    }
                },
                error: function (response) {
                    errorMessage.html(response);
                    errorMessage.show();
                }
            });
        });

    }

    return {
        init: Init
    }
}();
