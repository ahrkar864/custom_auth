$('.login_button').on('click',function(){
    $('#registerModal').modal('hide');
    $('#loginModal').modal('show')
})
$('.register_button').on('click',function(){
    $('#registerModal').modal('show');
    $('#loginModal').modal('hide')
})

function recaptchaDataCallbackRegister(response){
    $('#hiddenRecaptchaRegister').val(response);
    $('#hiddenRecaptchaRegisterError').html('');
}
  
function recaptchaExpireCallbackRegister(){
   $('#hiddenRecaptchaRegister').val('');
}

function recaptchaDataCallbackLogin(response){
    $('#hiddenRecaptchaLogin').val(response);
    $('#hiddenRecaptchaLoginError').html('');
}
  
function recaptchaExpireCallbackLogin(){
   $('#hiddenRecaptchaRegister').val('');
}

$('#registration_form').validate({
    ignore:'.ignore',
    errorClass:'invalid',
    validClass:'success',
    submitHandler:function(form){
        $.LoadingOverlay("show");
        form.submit();
    },
    rules: {
        first_name: {
            required: true,
            minlength: 2,
            maxlength: 100
        },
        last_name:{
            required: true,
            minlength: 2,
            maxlenght: 100
        },
        email:{
            required: true,
            email: true,
            remote: {
                url: window.baseUrl + "/auth/check_email_unique", // Use window.baseUrl
                type: "post",
                data: {
                    email: function() {
                        return $("#email").val();
                    },
                    '_token': $('meta[name="csrf-token"]').attr('content') // Use meta tag for CSRF token
                }
            }
        },
        password:{
            required: true,
            minlength: 2,
            maxlength: 100
        },
        confirm_password:{
            required: true,
            equalTo:"#password"
        },
        terms:"required",
        grecaptcha:"required"
    },
    messages: {
        first_name: {
            required: "Please enter your first name"
        },
        last_name:{
            required: "Please enter your last name"
        },
        email:{
            required: "We need your email address to contact you",
            email: "Your email address must be in the format of name@domain.com",
            remote:"Email already in use. Try with different email"
        },
        password:{
            required: "Enter your password"
        },
        confirm_password:{
            required: "Need to confirm your password"
        },
        terms: "Please accept our terms and conditions",
        grecaptcha: "Captcha field is required"
    },
    errorPlacement:function(error,element){
        if(element.attr('name')=='terms'){
            error.appendTo($('#terms_error'));
        } else if(element.attr('name')=='grecaptcha') {
            error.appendTo($('#hiddenRecaptchaRegisterError'));
        } else {
            error.insertAfter(element);
        }
    }

});


$('#login_form').validate({
    ignore:'.ignore',
    errorClass:'invalid',
    validClass:'success',
    rules: {
        email:{
            required: true,
            email: true,
        },
        password:{
            required: true,
            minlength: 2,
            maxlength: 100
        },
        terms:"required",
        grecaptcha:"required"
    },
    messages: {

        email:{
            required: "Email is required",
            email: "Your email address must be in the format of name@domain.com",
        },
        password:{
            required: "Enter your password"
        },
        confirm_password:{
            required: "Need to confirm your password"
        },
        terms: "Please accept our terms and conditions",
        grecaptcha: "Captcha field is required"
    },
    errorPlacement:function(error,element){
        if(element.attr('name')=='grecaptcha') {
            error.appendTo($('#hiddenRecaptchaLoginError'));
        } else {
            error.insertAfter(element);
        }
    }

})

  