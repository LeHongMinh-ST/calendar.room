$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

jQuery.validator.addMethod("noSpace", function(value, element) {
    return value == '' || value.trim().length != 0;
}, "Vui lòng không nhập khoảng trắng");

$('#resetForm').validate({
    errorClass: 'errors',
    rules:{
        old_password:{
            required:true,
            'noSpace':true,
            remote:{
                type: 'post',
                url: '/check-old-password',
            }
        },
        password:{
            required:true,
            'noSpace':true,
            minlength:6
        },
        password_confirm:{
            equalTo: "#password",
            'noSpace':true,
        }
    },
    messages:{
        old_password:{
            required:"Vui lòng mật khẩu cũ!",
            remote:"Mật khẩu cũ không chính xác!",
        },
        password:{
            required:"Vui lòng mật khẩu mới!",
            minlength:"Mật khẩu ít nhất 6 kí tự!"
        },
        password_confirm:{
            equalTo : "Mật khẩu không khớp! Vui lòng nhập lại",
        }
    }
});

$('#btnSubmitForm').click(function(e){
    e.preventDefault();
    if($('#resetForm').valid()) $('#resetForm').submit();
})