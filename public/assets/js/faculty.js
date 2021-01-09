jQuery( document ).ready(function( $ ) {
    $.ajaxSetup({headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
    });
    function css() {
        $('#listFaculty_wrapper').addClass('main_table');
        $('#listFaculty_paginate').addClass('pagination');
    }

    function dataTable() {
        let id = $(this).attr('data-id');
        var table = $('#listFaculty').DataTable({
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
            processing: true,
            serverSide: true,
            searching: true,
            destroy: true,
            responsive: true,
            ajax: {
                type: 'get',
                url: '/admin/faculty/getData',
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false},
                {data: 'faculty_id', name: 'faculty_id'},
                {data: 'name', name: 'name'},
                {data: 'is_active', name: 'is_active',orderable: false, searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        });
        css();
    }dataTable();

    $('#listFaculty').on('change', '.active_switch', function () {
        let id = $(this).attr('data-id');
        $.ajax({
            type: 'put',
            url: '/admin/faculty/status/' + id,
            success: function (response) {
                if ($('#active_switch' + id).prop('checked')) {
                    $('#active_switch' + id).prop('checked', true);
                    toastr.success('Bạn đã mở thành công khoa '+response.room, 'Mở khoa');
                } else {
                    $('#active_switch' + id).prop('checked', false);
                    toastr.warning('Bạn đã tạm khóa khoa '+response.room, 'Tạm khóa khoa');
                }
            }
        })
    });

    /* Sự kiện người dùng khi click vào nút xóa*/
    $('#listFaculty').on('click', '.btn-delete', function (event) {
        event.preventDefault();
        let id = $(this).attr('data-id');
        Swal.fire({
            title: 'Xóa khoa?',
            text: "Bạn có chắc chắn muốn xóa khoa máy này! Dữ liệu không thể khôi phục",
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
                    url: '/admin/faculty/delete/' + id,
                    success: function (response) {
                        if(response.statusCheck){
                            $('#listFaculty').DataTable().ajax.reload();
                            toastr.success('Bạn đã xóa thành công khoa ' + response.faculty_id, 'Xóa thành công');
                        }
                        else {
                            toastr.error('Bạn không thể xóa khoa '+ response.faculty_id + '. Vì khoa này đã được sử dụng', 'Xóa thất bại');
                        }
                    }
                });
            }
        })
    });

    //create
    $('.btn-add').click(function (e) {
        e.preventDefault();
        $('#formAddFaculty')[0].reset();
        $('#formAddFaculty').validate().resetForm();
        $('#addModalFaculty').modal('show');
    });

    $('#formAddFaculty').on('click','.btn-save',function(e){
        e.preventDefault();
        let id_semester = $(this).attr('data-id');
        if(!$('#formAddFaculty').valid()) return false;
        let data = $('#formAddFaculty').serialize();
        $.ajax({
            type:'post',
            url:'/admin/faculty/store',
            data: data,
            success:function(res){
                if(!res.error){
                    $('#listFaculty').DataTable().ajax.reload();
                    $('#addModalFaculty').modal('hide');
                    toastr.success(res.message, 'Thành công');
                }else{
                    toastr.error(res.message, 'Thất bại');
                }
            }

        })
    });

    $('#listFaculty').on('click','.btn-edit',function (e) {
        e.preventDefault();
        $('#formEditFaculty')[0].reset();
        $('#formEditFaculty').validate().resetForm();
        let id_faculty = $(this).attr('data-id');
        $('#btnSaveformEditFaculty').val(id_faculty);
        $.ajax({
            type: 'get',
            url: '/admin/faculty/' + id_faculty + '/edit',
            success:function(res){
                if(!res.error){
                    let faculty = res.faculty;
                    $('#edit_faculty_id').val(faculty.faculty_id);
                    $('#edit_faculty_name').val(faculty.name);
                    $("#editModalFaculty").modal('show');
                }
            }
        });
    });

    /*Sự kiện khi người dùng click vào nút lưu từ form sửa dữ liệu*/
    $('#formEditFaculty').on('click','.btn-save',function(e){
        if(!$('#formEditFaculty').valid()) return false;
        e.preventDefault();
        let id = $('#btnSaveformEditFaculty').val();;
        let data = $('#formEditFaculty').serialize();
        $.ajax({
            type:'put',
            url:'/admin/faculty/update/'+ id,
            data: data,
            success:function(res){
                if(!res.error){
                    $('#listFaculty').DataTable().ajax.reload();
                    $('#editModalFaculty').modal('hide');
                    toastr.success(res.message, 'Thành công');
                }else{
                    toastr.error(res.message, 'Thất bại');
                }
            }
        });
    });

    /*Kiểm tra lỗi cho form thêm mới*/
    jQuery.validator.addMethod("CheckUniqueFacultyID", function(value, element) {
        var status = null;
        $.ajax({
            url: "/admin/faculty/check-faculty-id-unique",
            type: "post",
            data: {
                faculty_id: function() {
                    return $( "#add_faculty_id" ).val();
                },
            },
            async: false,
            success: (res) => {
                status = res;
            },
            error: function (jqXHR, textStatus, errorThrown) {
            }
        });
        return status;
    });

    jQuery.validator.addMethod("CheckUniqueFacultyName", function(value, element) {
        var status = null;
        $.ajax({
            url: "/admin/faculty/check-faculty-name-unique",
            type: "post",
            data: {
                name: function() {
                    return $( "#add_faculty_name" ).val();
                }
            },
            async: false,
            success: (res) => {
                status = res;
            },
            error: function (jqXHR, textStatus, errorThrown) {
            }
        });
        return status;
    });

    $('#formAddFaculty').validate({
        errorClass: 'errors',
        rules:{
            add_faculty_id:{
                required: true,
                "CheckUniqueFacultyID": true,
            },
            add_faculty_name:{
                required: true,
                "CheckUniqueFacultyName": true,
            },
        },
        messages:{
            add_faculty_id:{
                required: "Vui lòng nhập mã khoa",
                "CheckUniqueFacultyID": "Mã khoa này đã tồn tại, vui lòng thử lại với mã khác"
            },
            add_faculty_name:{
                required: "Vui lòng nhập tên khoa",
                "CheckUniqueFacultyName": "Tên khoa này đã tồn tại, vui lòng thử với tên khác"
            },
        },
    });

    jQuery.validator.addMethod("CheckUniqueFacultyIDEdit", function(value, element) {
        var status = null;
        $.ajax({
            url: "/admin/faculty/check-faculty-id-unique",
            type: "post",
            data: {
                id: function() {
                    return $( "#btnSaveformEditFaculty" ).val();
                },
                faculty_id: function() {
                    return $( "#edit_faculty_id" ).val();
                },
            },
            async: false,
            success: (res) => {
                status = res;
            },
            error: function (jqXHR, textStatus, errorThrown) {
            }
        });
        return status;
    });

    jQuery.validator.addMethod("CheckUniqueFacultyNameEdit", function(value, element) {
        var status = null;
        $.ajax({
            url: "/admin/faculty/check-faculty-name-unique",
            type: "post",
            data: {
                id: function() {
                    return $( "#btnSaveformEditFaculty" ).val();
                },
                name: function() {
                    return $( "#edit_faculty_name" ).val();
                }
            },
            async: false,
            success: (res) => {
                status = res;
            },
            error: function (jqXHR, textStatus, errorThrown) {
            }
        });
        return status;
    });

    $('#formEditFaculty').validate({
        errorClass: 'errors',
        rules:{
            edit_faculty_id:{
                required: true,
                "CheckUniqueFacultyIDEdit": true,
            },
            edit_faculty_name:{
                required: true,
                "CheckUniqueFacultyNameEdit": true,
            },
        },
        messages:{
            edit_faculty_id:{
                required: "Vui lòng nhập mã khoa",
                "CheckUniqueFacultyIDEdit": "Mã khoa này đã tồn tại, vui lòng thử lại với mã khác"
            },
            edit_faculty_name:{
                required: "Vui lòng nhập tên khoa",
                "CheckUniqueFacultyNameEdit": "Tên khoa này đã tồn tại, vui lòng thử với tên khác"
            },
        },
    });

});
