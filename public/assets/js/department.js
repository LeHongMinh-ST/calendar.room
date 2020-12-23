
// jQuery.noConflict();

jQuery( document ).ready(function( $ ) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $('#faculty_id').select2({placeholder: "Vui lòng chọn khoa",});
    $('#filterFaculty').select2();

    function css() {
        $('#listDepartment_wrapper').addClass('main_table');
        $('#listDepartment_paginate').addClass('pagination');
    }
    function dataTable(facultyId=null){
        var table= $('#listDepartment').DataTable({
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
                url: '/admin/department/getData',
                data:{'faculty_id': facultyId},
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false},
                { data: 'department_id', name: 'department_id',searchable: true },
                { data: 'name', name: 'name'},
                { data: 'faculty_id', name: 'faculty_id'},
                {data: 'is_active', name: 'is_active', orderable: false, searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        });
        css();
    }
    dataTable();

    // Sự kiện khi lọc dữ liệu theo tên khoa
    $("#filter").click(function(e){
        e.preventDefault();
        let facultyId = $("#filterFaculty").val();
        if(facultyId != null){
            dataTable(facultyId);
        }
    });

    // Sự kiện reset lại bộ lọc của bảng
    $("#resetFilter").click(function(e){
        e.preventDefault();
        $("#filterFaculty").select2().val(null).trigger('change');
        dataTable();
    });

    // Sự kiện click vào nút thêm mới để hiện form thêm mới
    $('#btn-add').on('click',function (e) {
            e.preventDefault();
            $('#frmAdd')[0].reset();
            $('#frmAdd').validate().resetForm();
            $('#faculty_id').val(null).trigger('change');
            $('#AddModal').modal('show');
    });

    $('#listDepartment').on('change', '.active_switch', function () {
        let id = $(this).attr('data-id');
        $.ajax({
            type: 'put',
            url: '/admin/department/toogleActive/' + id,
            success: function (response) {
                if ($('#active_switch' + id).prop('checked')) {
                    $('#active_switch' + id).prop('checked', true);
                    toastr.success('Bạn đã kích hoạt thành công bộ môn  '+response.department, 'Kích hoạt bộ môn');
                } else {
                    $('#active_switch' + id).prop('checked', false);
                    toastr.warning('Bạn đã tạm khóa thành công bộ môn '+response.department, 'Khóa bộ môn');
                }
            }
        })
    });

    //Sự kiện click vào nút lưu từ form tạo mới
    $('#frmAdd').on('click','.btn-primary',function(e){
        e.preventDefault();
        if(!$('#frmAdd').valid()) return false;
        $.ajax({
            type:'post',
            url:'/admin/department/store',
            data: {
                department_id   : $('#department_id').val(),
                name   : $('#name').val(),
                faculty_id: $('#faculty_id').val(),
            },
            success:function(res){
                if(!res.error){
                    $('#listDepartment').DataTable().ajax.reload();
                    $('#AddModal').modal('hide');
                    toastr.success(res.message, 'Thành công');
                }else{
                    toastr.error(res.message, 'Thất bại');
                }
            }
        })
    });

    //Sự kiện click vào nút sửa để hiện form sửa thông tin
    $('#listDepartment').on('click','.btn-edit',function (e) {
        e.preventDefault();
        $('#frmEdit')[0].reset();
        $('#frmEdit').attr('data-id',$(this).attr('data-id'));
        $('#frmEdit').validate().resetForm();

        let id = $(this).attr('data-id');
        $('#btnSaveformEdit').val(id);
        $.ajax({
            type:'get',
            url: '/admin/department/'+ id +'/edit',
            success:function(res){
                if(!res.error)
                {
                    $('#faculty_id_edit').val(res.department.faculty_id).select2().trigger('change');
                    $('#department_id_edit').val(res.department.department_id);
                    $('#name_edit').val(res.department.name);

                    $('#frmEdit').attr('data-id',id);
                    $('#EditModal').modal('show');
                }
            }
        });
    });

    //Sự kiện click vào nút lưu từ form sửa
    $('#frmEdit').on('click','.btn-save',function(e){
        if(!$('#frmEdit').valid()) return false;
        e.preventDefault();
        let id = $('#btnSaveformEdit').val();
        $.ajax({
            type:'put',
            url:'/admin/department/update/'+ id,
            data: {
                department_id   : $('#department_id_edit').val(),
                name   : $('#name_edit').val(),
                faculty_id: $('#faculty_id_edit').val(),
            },
            success:function(res){
                if(!res.error){
                    $('#listDepartment').DataTable().ajax.reload();
                    $('#EditModal').modal('hide');
                    toastr.success(res.message, 'Thành công');
                }else{
                    toastr.error(res.message, 'Thất bại');
                }
            }
        });
    });

    //sự kiện click vào nút xóa từ form delete
    $('#listDepartment').on('click', '.btn-delete', function (event) {
        event.preventDefault();
        let id = $(this).attr('data-id');
        Swal.fire({
            title: 'Xóa lịch trực?',
            text: "Bạn có chắc chắn muốn bộ môn này! Dữ liệu không thể khôi phục",
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
                    url: '/admin/department/destroy/' + id,
                    success: function (response) {
                        if(response.checkAssignment){
                            $('#listDepartment').DataTable().ajax.reload();
                            toastr.success(response.message, 'Xóa thành công');
                        }
                        else {
                            toastr.error(response.message, 'Xóa thất bại');
                        }
                    }
                });
            }
        })
    });

    // Check validate
    jQuery.validator.addMethod("lettersonlys", function(value, element) {
        return this.optional(element) || /^[a-zA-Z0-9]*$/.test(value) && !/\s/.test(value);
    }, "Vui lòng nhập chuỗi không có kí tự đặc biệt, viết liền không dấu !");
    jQuery.validator.addMethod("noSpace", function(value, element) {
        return value == '' || value.trim().length != 0;
    }, "Vui lòng không nhập khoảng trắng!");

    // Kiểm tra mã bộ môn là duy nhất
    jQuery.validator.addMethod("CheckUniqueDepartment", function(value, element) {
        var status = null;
        $.ajax({
            url: "/admin/department/check-department-id-unique",
            type: "post",
            data: {
                department_id: function() { return $( "#department_id" ).val();}
            },
            async: false,
            success: (res) => {
                if(res == 'false') status = false;
                else status = true;
            },
            error: function (jqXHR, textStatus, errorThrown) {
            }
        });
        return status;
    });

    // Kiểm tra tên bộ môn là duy nhất
    jQuery.validator.addMethod("CheckUniqueNameDepartment", function(value, element) {
        var status = null;
        $.ajax({
            url: "/admin/department/check-name-unique",
            type: "post",
            data: {
                name: function() { return $( "#name" ).val(); }
            },
            async: false,
            success: (res) => {
                if(res == 'false') status = false;
                else status = true;
            },
            error: function (jqXHR, textStatus, errorThrown) {
            }
        });
        return status;
    });

    $('#frmAdd').validate({
        errorClass: 'errors',
        rules:{
            faculty_id:{ required:true, },
            department_id:{ required: true, "lettersonlys": true, "CheckUniqueDepartment":true, },
            name:{ required:true, "noSpace": true, "CheckUniqueNameDepartment":true, },
        },
        messages:{
            faculty_id:{ required:"Vui lòng chọn khoa"},
            department_id:{required:"vui lòng nhập mã bộ môn!", "CheckUniqueDepartment": "Vui lòng nhập mã khác. Mã bộ môn này đã tồn tại!"},
            name:{required:"Vui lòng nhập tên bộ môn!", "CheckUniqueNameDepartment":"Vui lòng nhập tên khác. Tên bộ môn này đã tồn tại!"},
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

    // Kiểm tra mã bộ môn là duy nhất khi dùng phương thức sửa
    jQuery.validator.addMethod("CheckUniqueIdDepartmentUpdate", function(value, element) {
        var status = null;
        $.ajax({
            url: "/admin/department/check-department-id-unique-update",
            type: "post",
            data: {
                department_id: function() { return $( "#department_id_edit" ).val(); },
                id: function () { return $('#frmEdit').attr('data-id'); }
            },
            async: false,
            success: (res) => {
                if(res == 'false') status = false;
                else status = true;
            },
            error: function (jqXHR, textStatus, errorThrown) {
            }
        });
        return status;
    });

    // Kiểm tra tên bộ môn là duy nhất khi dùng phương thức sửa
    jQuery.validator.addMethod("CheckUniqueNameDepartmentUpdate", function(value, element) {
        var status = null;
        $.ajax({
            url: "/admin/department/check-name-unique-update",
            type: "post",
            data: {
                name: function() { return $( "#name_edit" ).val(); },
                id: function () { return $('#frmEdit').attr('data-id'); }
            },
            async: false,
            success: (res) => {
                if(res == 'false') status = false;
                else status = true;
            },
            error: function (jqXHR, textStatus, errorThrown) {
            }
        });
        return status;
    });

    // Kiểm tra tên bộ môn là duy nhất khi dùng phương thức sửa
    jQuery.validator.addMethod("checkIsActiveFaculty", function(value, element) {
        var status = null;
        $.ajax({
            url: "/admin/department/check-isActive-faculty",
            type: "post",
            data: {
                faculty_id: function() { return $( "#faculty_id_edit" ).val(); },
                department_id: function () { return $('#frmEdit').attr('data-id'); }
            },
            async: false,
            success: (res) => {
                if(res == 'false') status = false;
                else status = true;
            },
            error: function (jqXHR, textStatus, errorThrown) {
            }
        });
        return status;
    });

    $('#frmEdit').validate({
        errorClass: 'errors',
        rules:{
            faculty_id_edit: {"checkIsActiveFaculty": true},
            department_id_edit:{ required: true, "lettersonlys": true, "CheckUniqueIdDepartmentUpdate": true, },
            name_edit:{ required:true, "noSpace": true, "CheckUniqueNameDepartmentUpdate": true, },
        },
        messages:{
            faculty_id_edit: {"checkIsActiveFaculty": "Khoa này tạm thời đã bị khóa vui lòng chọn khoa khác"},
            department_id_edit:{ required:"vui lòng nhập mã bộ môn!", "CheckUniqueIdDepartmentUpdate": "Vui lòng nhập mã khác. Mã bộ môn này đã tồn tại!"},
            name_edit:{ required:"Vui lòng nhập tên bộ môn!", "CheckUniqueNameDepartmentUpdate": "Vui lòng nhập tên khác. Tên bộ môn này đã tồn tại!"},
        },errorPlacement: function(error, element) {
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
});
