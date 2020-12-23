$('#add_department_id').select2({
    placeholder: 'Vui lòng chọn bộ môn',
    witdh: '100%'
});
jQuery( document ).ready(function( $ ) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    function css() {
        console.log('css');
        $('#listSubject_wrapper').addClass('main_table');
        $('#listSubject_paginate').addClass('pagination');
    }
    function dataTable(){
        var table= $('#listSubject').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            destroy: true,
            responsive: true,
            ajax: {
                url: '/admin/subject/getData',
            },
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
            columns: [
                { data: 'subject_id', name: 'subjects.subject_id' },
                { data: 'name', name: 'subjects.name' },
                { data: 'department', name: 'department' },
                { data: 'user_create', name: 'user_create' },
                {data: 'is_active', name: 'is_active', orderable: false, searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        });
        css();
    }
    dataTable();

    $('#listSubject').on('click', '.btn-delete', function (event) {
        event.preventDefault();
        let id = $(this).attr('data-id');
        Swal.fire({
            title: 'Xóa môn học?',
            text: "Bạn có chắc chắn muốn xóa môn học này! Dữ liệu không thể khôi phục",
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
                    url: '/admin/subject/destroy/' + id,
                    success: function (response) {
                        $('#listSubject').DataTable().ajax.reload();
                        toastr.success('Bạn đã xóa thành công môn học ' + response.name, 'Xóa thành công');
                    }
                });
            }
        })

    })
    jQuery.validator.addMethod("noSpace", function(value, element) {
        return value == '' || value.trim().length != 0;
    }, "Vui lòng không nhập khoảng trắng!");
    $('#add_subject').on('click', function(e){
        e.preventDefault();
        $('#add_form')[0].reset();
        $('#addModal').modal('show');
    });
    $('#add_form').validate({
        errorElement: "span",
        rules: {
            subject_id: {
                required: true,
                "noSpace":true,
                minlength: 3,
                maxlength: 15,
                remote:{
                    url: "/admin/subject/check-subject-id-unique",
                    type: "post",
                    data: {
                        user_name: function() {
                            return $( "#add_subject_id" ).val();
                        }
                    }
                }

            },
            department_id: {
                required: true
            },
            name: {
                required: true,
                "noSpace":true,
                minlength: 3,
                maxlength: 50
            }
        },
        messages: {
            subject_id: {
                required: "Vui lòng nhập mã môn học",
                minlength: "Mã môn học không được dưới 3 kí tự",
                maxlength: "Mã môn học không dài hơn 15 kí tự",
                remote: "Mã môn học đã tồn tại"
            },
            name: {
                required: "Vui lòng nhập tên môn học",
                minlength: "Tên môn học không được dưới 3 kí tự",
                maxlength: "Tên môn học không nhiều hơn 50 kí tự"
            },
            department_id: {
                required: "Vui lòng chọn bộ môn",
            },

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
    })
    $('#edit_form').validate(
        {
            errorElement: "span",
            rules: {
                subject_id: {
                    required: true,
                    "noSpace":true,
                    minlength: 3,
                    maxlength: 15,
                    remote:{
                        url: "/admin/subject/check-subject-id-unique-update",
                        type: "post",
                        data: {
                            name: function() {
                                return $( "#edit_subject_id" ).val();
                            },
                            id: function(){
                                return $('#edit_form').data('id');
                            }
                        }
                    }
                },
                department_id: {
                    required: true
                },
                name: {
                    required: true,
                    "noSpace":true,
                    minlength: 3,
                    maxlength: 50
                }
            },
            messages: {
                subject_id: {
                    required: "Vui lòng nhập mã môn học",
                    minlength: "Mã môn học không được dưới 3 kí tự",
                    maxlength: "Mã môn học không dài hơn 15 kí tự",
                    remote:"Mã môn học đã tồn tại!"
                },
                name: {
                    required: "Vui lòng nhập tên môn học",
                    minlength: "Tên môn học không được dưới 3 kí tự",
                    maxlength: "Tên môn học không nhiều hơn 50 kí tự"
                },
                department_id: {
                    required: "Vui lòng chọn bộ môn",
                },

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
        })
    $('#add-save').on('click', function(e){
        e.preventDefault();
        if (!$('#add_form').valid()) {
            return false;
        }
        $.ajax({
            url: '/admin/subject/store',
            type: 'POST',
            data: {
                subject_id: $('#add_subject_id').val(),
                name: $('#add_name').val(),
                department_id: $('#add_department_id').val(),
            },
            success: function (res) {
                if (!res.error) {
                    $('#listSubject').DataTable().ajax.reload();
                    $('#addModal').modal('hide');
                    toastr.success("Thêm mới môn học thành công");
                } else {
                    toastr.error(res.message);
                }
            }

        })
    })
    $('#listSubject').on('click','.btn-edit', function(e){
        e.preventDefault();
        let id= $(this).data('id');
        $.ajax({
            url: "/admin/subject/"+id+"/edit",
            type: 'GET',
            success: function(res){
                $('#edit_form')[0].reset();
                $('#edit_form').validate().resetForm();
                $('#editModal').modal('show');
                $('#edit_name').val(res.subject.name);
                $('#edit_subject_id').val(res.subject.subject_id);
                $('#edit_department_id').select2().val(res.subject.department_id).trigger('change');
                $('.edit-save').attr('data-id',res.subject.id);
                $('#edit_form').attr('data-id',res.subject.id);
            }
        })
    })
    $('.edit-save').on('click',function(e){
        let id= $(this).data('id');
        e.preventDefault();
        if (!$('#edit_form').valid()) {
            return false;
        }
        $.ajax({
            url: '/admin/subject/update/'+id,
            type: 'PUT',
            data: {
                subject_id: $('#edit_subject_id').val(),
                name: $('#edit_name').val(),
                department_id: $('#edit_department_id').val(),
            },
            success: function (res) {
                console.log('data: '+res.data);

                if (!res.error) {
                    $('#listSubject').DataTable().ajax.reload();
                    $('#editModal').modal('hide');
                    toastr.success("Cập nhật môn học thành công");
                } else {
                    $('#editModal').modal('hide');

                    toastr.error(res.message);
                }
            },
        })
    });
    $('#listSubject').on('change', '.active_switch', function () {
        let id = $(this).attr('data-id');
        $.ajax({
            type: 'put',
            url: '/admin/subject/toggleActive/' + id,
            success: function (response) {
                if ($('#active_switch' + id).prop('checked')) {
                    $('#active_switch' + id).prop('checked', true);
                    toastr.success('Bạn đã kích hoạt thành công môn học '+response.subject, 'Kích hoạt môn học');
                } else {
                    $('#active_switch' + id).prop('checked', false);
                    toastr.warning('Bạn đã tạm khóa thành công môn học '+response.subject, 'Khóa môn học');
                }
            }
        })

    });
});

