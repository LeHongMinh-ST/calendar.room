$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#department').select2({
        placeholder: "Vui lòng chọn bộ môn",
        dropdownParent: $('#addUserModal')
    });
    $('#edit_department').select2({
        tags: true,
        dropdownParent: $('#editUserModal')
    });
    $('#role').select2({
        placeholder: "Vui lòng chọn vai trò người dùng",
        dropdownParent: $('#addUserModal')
    });
    $('#edit_role').select2({
        dropdownParent: $('#editUserModal')
    });



    function css() {
        // $('#table_user_wrapper').addClass('main_table');
        $('#table_user_paginate').addClass('pagination');
    }


    var UserDataTable =  $('#table_user').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            destroy: true,
            // responsive: true,
            language: {
                sProcessing:   "Đang xử lý...",
                sLengthMenu:   "Xem _MENU_ mục",
                sZeroRecords:  "Không tìm thấy dòng nào phù hợp",
                sInfo:         "Đang xem _START_ đến _END_ trong tổng số _TOTAL_ mục",
                sInfoEmpty:    "Đang xem 0 đến 0 trong tổng số 0 mục",
                sInfoFiltered: "(được lọc từ _MAX_ mục)",
                sSearch: 'Tìm kiếm',
                lengthMenu: '_MENU_ bản ghi/trang',
                oPaginate: {
                    "sFirst":    "Đầu",
                    "sPrevious": "Trước",
                    "sNext":     "Tiếp",
                    "sLast":     "Cuối"
                }
            },
            ajax: {
                "type": "get",
                "url": "/admin/users/getdata",
            },
            columns: [
                {data: 'DT_RowIndex', orderable: false,searchable: false, class:'text-center'},
                {data: 'user_name', name: 'user_name', orderable: true, searchable: true},
                {data: 'full_name', name: 'full_name', orderable: true, searchable: true},
                {data: 'email', name: 'email', orderable: false, searchable: true},
                {data: 'role_id', name: 'role_id', orderable: true, searchable: false},
                {data: 'phone', name: 'phone', orderable: false, searchable: false},
                {data: 'department_id', name: 'department_id', orderable: false, searchable: false},
                {data: 'is_active', name: 'is_active', orderable: false, searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        });
        css();

    $('#table_user').on('click', '.btn-delete', function (event) {
        event.preventDefault();
        let id = $(this).attr('data-id');

        Swal.fire({
            title: 'Xóa người dùng?',
            text: "Bạn có chắc chắn muốn xóa người dùng này! Dữ liệu không thể khôi phục",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Đồng ý!',
            cancelButtonText: 'Đóng'
          }).then((result) => {
            if (result.value) {
                $.ajax({
                    type: 'delete',
                    url: '/admin/users/delete/' + id,
                    success: function (response) {
                        $('#table_user').DataTable().ajax.reload();
                        toastr.success('Bạn đã xóa thành công tài khoản ' + response.name, 'Xóa thành công');
                    }
                });
            }
          })
    });
    $('#table_user').on('change', '.active_switch', function () {
        let id = $(this).attr('data-id');
        $.ajax({
            type: 'put',
            url: '/admin/users/role/' + id,
            success: function (response) {
                if ($('#active_switch' + id).prop('checked')) {
                    $('#active_switch' + id).prop('checked', true);
                    toastr.success('Bạn đã kích hoạt thành công tài khoản '+response.user, 'Kích hoạt tài khoản');
                } else {
                    $('#active_switch' + id).prop('checked', false);
                    toastr.warning('Bạn đã tạm khóa thành công tài khoản '+response.user, 'Khóa tài khoản');
                }
            }
        })

    });

    $('#btnAddUser').click(function(e){
        e.preventDefault();
        $('#formAddUser')[0].reset();
        $('#formAddUser').validate().resetForm();
        $('#department').val(null).trigger('change');
        $('#role').val(null).trigger('change');
        $('#addUserModal').modal('show');
    });

    jQuery.validator.addMethod("lettersonlys", function(value, element) {
        return this.optional(element) || /^[a-zA-Z0-9]*$/.test(value) && !/\s/.test(value);
      }, "Vui lòng nhập chuỗi không có kí tự đặc biệt, viết liền không dấu !");

    $('#formAddUser').validate({
        errorClass: 'errors',
        rules:{
            user_name:{
                required: true,
                "lettersonlys": true,
                remote:{
                    url: "/admin/users/check-user-name-unique",
                    type: "post",
                    data: {
                        user_name: function() {
                            return $( "#user_name" ).val();
                        }
                    }
                }
            },
            full_name:{
                required:true,
            },
            email:{
                required:true,
                email:true,
                remote:{
                    url: "/admin/users/check-email-unique",
                    type: "post",
                    data: {
                        email: function() {
                            return $( "#email" ).val();
                        }
                    }
                }
            },
            department:{
                required: true
            },
            role:{
                required: true
            },
            phone:{
                number: true,
            }
        },
        messages:{
            user_name:{
                required:"vui lòng nhập tên đăng nhập!",
                remote:"Vui lòng nhập tên đăng nhập khác. Tên đăng nhập này đã tồn tại!"
            },
            full_name:{
                required:"Vui lòng nhập họ và tên!",
            },
            email:{
                required:"Vui lòng nhập email!",
                email:"Vui lòng nhập đúng định dạng email!",
                remote:"Vui lòng nhập email khác. Email này đã tồn tại!"
            },
            department:{
                required: "Vui lòng chọn bộ môn",
            },
            role:{
                required: "Vui lòng chọn vai trò",
            },
            phone:{
                number: "Số điện thoại phải là chữ số !"
            }

        },
        errorPlacement: function(error, element) {
            let id = element.attr('id');
            if(element.hasClass('select2-input')){
                error.insertAfter($('#select2-'+id+'-container').parent(element));
            }else if(element.hasClass('datepicker')){
                error.insertAfter($('#'+id).parent(element));
            }else{
                error.insertAfter(element);
            }
        }
    });
    $('#formEditUser').validate({
        errorClass: 'errors',
        rules:{
            edit_user_name:{
                required: true,
                "lettersonlys": true,
                remote:{
                    url: "/admin/users/check-user-name-unique-update",
                    type: "post",
                    data: {
                        user_name: function() {
                            return $( "#edit_user_name" ).val();
                        },
                        id: function(){
                            return $('#formEditUser').attr('data-id');
                        }
                    }
                }
            },
            edit_full_name:{
                required:true,
            },
            edit_email:{
                required:true,
                email:true,
                remote:{
                    url: "/admin/users/check-email-unique-update",
                    type: "post",
                    data: {
                        email: function() {
                            return $( "#edit_email" ).val();
                        },
                        id: function(){
                            return $('#formEditUser').attr('data-id');
                        }
                    }
                }
            },
            edit_department:{
                required: true
            },
            edit_role:{
                required: true
            },
            edit_phone:{
                number:true,
            }
        },
        messages:{
            edit_user_name:{
                required:"vui lòng nhập tên đăng nhập!",
                remote:"Vui lòng nhập tên đăng nhập khác. Tên đăng nhập này đã tồn tại!"
            },
            edit_full_name:{
                required:"Vui lòng nhập họ và tên!",
            },
            edit_email:{
                required:"Vui lòng nhập email!",
                email:"Vui lòng nhập đúng định dạng email!",
                remote:"Vui lòng nhập email khác. Email này đã tồn tại!"
            },
            edit_department:{
                required: "Vui lòng chọn bộ môn",
            },
            edit_role:{
                required: "Vui lòng chọn vai trò",
            },
            edit_phone:{
                number: "Số điện thoại phải là số!"
            }
        },
        errorPlacement: function(error, element) {
            let id = element.attr('id');
            if(element.hasClass('select2-input')){
                error.insertAfter($('#select2-'+id+'-container').parent(element));
            }else if(element.hasClass('datepicker')){
                error.insertAfter($('#'+id).parent(element));
            }else{
                error.insertAfter(element);
            }
        }
    });

    $('#formAddUser').on('submit',function(e){
        e.preventDefault();
        if(!$('#formAddUser').valid()) {
            return false;
        }

        let data = $('#formAddUser').serialize();

        $.ajax({
            type:'post',
            url:'/admin/users',
            data: data,
            success:function(res){
                if(!res.error){
                    $('#table_user').DataTable().ajax.reload();
                    $('#addUserModal').modal('hide');
                    toastr.success(res.message, 'Thành công');
                }else{
                    toastr.error(res.message, 'Thất bại');
                }
            }

        })
    })

    $('#table_user').on('click','.btn-edit',function (e) {
        e.preventDefault();
        $('#formEditUser')[0].reset();
        $('#formEditUser').validate().resetForm();

        let id = $(this).attr('data-id');
        $.ajax({
            type:'get',
            url: '/admin/users/'+ id +'/edit',
            success:function(res){
                let user = res.user;
                if(!res.error)
                {
                    $('#edit_user_name').val(user.user_name);
                    $('#edit_full_name').val(user.full_name);
                    $('#edit_department').val(user.department_id).select2().trigger('change');
                    $('#edit_role').val(user.role_id).select2().trigger('change');

                    $('#edit_email').val(user.email);
                    $('#edit_phone').val(user.phone);
                    $('#edit_note').val(user.note);

                    $('#formEditUser').attr('data-id',id);
                    $(editUserModal).modal('show');
                }
            }
        });
    });

    $('#formEditUser').on('submit',function(e){
        if(!$('#formEditUser').valid()) return false;
        e.preventDefault();

        let id = $(this).attr('data-id');

        $.ajax({
            type:'put',
            url:'/admin/users/update/'+ id,
            data: {
                user_name   : $('#edit_user_name').val(),
                full_name   : $('#edit_full_name').val(),
                department  : $('#edit_department').val(),
                phone       : $('#edit_phone').val(),
                role        : $('#edit_role').val(),
                email       : $('#edit_email').val(),
                note        : $('#edit_note').val(),
            },
            success:function(res){
                if(!res.error){
                    $('#table_user').DataTable().ajax.reload();
                    $('#editUserModal').modal('hide');
                    toastr.success(res.message, 'Thành công');
                }else{
                    toastr.error(res.message, 'Thất bại');
                }
            }
        });
    })
});
