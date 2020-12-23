$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

jQuery.validator.addMethod("noSpace", function(value, element) {
    return value == '' || value.trim().length != 0;
}, "Vui lòng không nhập khoảng trắng");

$('#formEditInfo').validate({
    rules:{
        full_name:{
            required:true,
            "noSpace":true,
        },
        email:{
            required:true,
            email:true,
            "noSpace":true,
        }
    },
    messages:{
        full_name:{
            required: "Vui lòng nhập họ và tên!",
        },
        email:{
            requird: "Vui lòng nhập email!",
            email:"Vui lòng nhập đúng định dạng email"
        }
    }
})

$('#formEditInfo').on('submit',function(e){
    e.preventDefault()
    if(!$('#formEditInfo').valid()) return false;
    let data = $('#formEditInfo').serialize();

    $.ajax({
        type:'put',
        url:'/update-info',
        data:data,
        success:function(res){
            if(!res.error){
                toastr.success(res.message, 'Thành công');
            }else{
                toastr.error(res.message, 'Thất bại');
            }
        }
    })
})