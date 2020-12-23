$(document).ready(function () {

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    $("#filterSemester").select2();
    $('#filterRoom').select2();
    $("#room_id_edit").select2();
    $('#room_id_create').select2({placeholder: "Vui lòng chọn phòng máy",});

    /*
    * Hàm thêm class vào bảng giúp giao diện đẹp hơn*/
    function addCss(){
        $('#listAssignment_wrapper ').addClass('main_table');
        $('#listAssignment_paginate ').addClass('pagination');
    }



    /*
    * Hàm lấy dữ liệu để tạo bảng
    * @param semester_id: id học kỳ khi có yêu cầu lọc
    * @param room_id: id phòng máy khi có yêu cầu lọc */
    function dataTable(semester_id=null,room_id=null){
        var table = $("#listAssignment").DataTable({
            language: {
                sProcessing:   "Đang xử lý...", sLengthMenu:   "Xem _MENU_ mục", sZeroRecords:  "Không tìm thấy dòng nào phù hợp",
                sInfo:         "Đang xem _START_ đến _END_ trong tổng số _TOTAL_ mục", sInfoEmpty:    "Đang xem 0 đến 0 trong tổng số 0 mục",
                sInfoFiltered: "(được lọc từ _MAX_ mục)", sSearch: 'Tìm kiếm', lengthMenu: '_MENU_ bản ghi/trang',
                oPaginate: { "sFirst":    "Đầu", "sPrevious": "Trước", "sNext":     "Tiếp", "sLast":     "Cuối" }
            }, processing: true, serverSide: true, searching: true, destroy: true, responsive: true,
            ajax: {
                type: 'get',
                url: '/admin/assignment/getData',
                data:{ 'semester_id': semester_id, 'room_id':room_id, },

            },
            columns: [
                {data: 'DT_RowIndex', searchable: false},
                {data: 'room_id', name: 'room_id'},
                {data: 'semester_id', name: 'semester_id'},
                {data: 'technicians_name', name: 'technicians_name'},
                {data: 'start_date', name: 'start_date'},
                {data: 'end_date', name: 'end_date'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        });
        addCss();
    }
    dataTable();

    /*
    * Sự kiện khi lọc dữ liệu theo học kỳ hoặc phòng máy*/
    $("#filter").click(function(e){
        e.preventDefault();
        let semester_id = $("#filterSemester").val();
        let room_id = $("#filterRoom").val();
        if(semester_id != null || room_id != null){
            dataTable(semester_id,room_id);
        }
    });

    /*
    * Sự kiện reset lại bộ lọc của bảng*/
    $("#resetFilter").click(function(e){
        e.preventDefault();
        $("#filterSemester").select2().val(null).trigger('change');
        $("#filterRoom").select2().val(null).trigger('change');
        dataTable();
    });

    /*
    * Sự kiện người dùng khi click vào nút xóa*/
    $('#listAssignment').on('click', '.btn-delete', function (event) {
        event.preventDefault();
        let id = $(this).attr('data-id');
        Swal.fire({
            title: 'Xóa lịch trực?',
            text: "Bạn có chắc chắn muốn lịch trực này! Dữ liệu không thể khôi phục",
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
                    url: '/admin/assignment/delete/' + id,
                    success: function (response) {
                        if(response.checkAssignment){
                            $('#listAssignment').DataTable().ajax.reload();
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

    /*
    * Sự kiện khi người dùng click vào nút thêm mới sẽ xuất hiện form thêm mới*/
    $('#btnAddAssignment').click(function (e) {
        e.preventDefault();
        let id_semester = $(this).val(); //Lấy ra id học kỳ hiện tại
        //Nếu chưa có id học kỳ thì không thể thêm lịch.
        if(id_semester == ''){
            alert("Vui lòng thêm học kỳ hiện tại để thêm mới lịch trực!");
            // $("#123").text("<strong>Danger!</strong>");
        }else {
            $('#formAddAssignment')[0].reset();
            $('#formAddAssignment').validate().resetForm();
            $('#room_id_create').val(null).trigger('change');
            $('#addAssignmentModal').modal('show');

            getTime("#time_assignment_create",id_semester);
        }
    });

    /*Sự kiện khi người dùng click vào nút lưu từ form thêm mới*/
    $('#formAddAssignment').on('click','.btn-show',function(e){
        e.preventDefault();
        let id_semester = $(this).attr('data-id');
        if(!$('#formAddAssignment').valid()) return false;
        let data = $('#formAddAssignment').serialize();
        data = data+'&id_semester='+id_semester;
        $.ajax({
            type:'post',
            url:'/admin/assignment/store',
            data: data,
            success:function(res){
                if(!res.error){
                    $('#listAssignment').DataTable().ajax.reload();
                    $('#addAssignmentModal').modal('hide');
                    toastr.success(res.message, 'Thành công');
                }else{
                    toastr.error(res.message, 'Thất bại');
                }
            }

        })
    });

    /*Sự kiện người dùng click vào nút xem chi tiết*/
    $('#listAssignment').on('click','.btn-show',function (e) {
        e.preventDefault();
        let id = $(this).attr('data-id');

        $.ajax({
            type:'get',
            url: '/admin/assignment/show/'+ id,
            success:function(response){
                let assignment = response.assignment
                $('#show_room').text(assignment.room_id);
                $('#show_semester').text(assignment.semester_id);
                $('#show_technicians_name').text(assignment.technicians_name);
                $('#show_phone').text(assignment.phone);
                $('#show_time_assingment').text(response.date);
            },
            error: function (error) {
                console.log(error);
            }
        });
        $('#showModal').modal('show');
    });

    /*
    * Sự kiện hiện form chỉnh sử dữ liệu khi click vào nút sửa*/
    $('#listAssignment').on('click', '.btn-edit', function (e) {
        e.preventDefault();
        $('#formEditAssignment')[0].reset();
        $('#formEditAssignment').validate().resetForm();
        let id_assignment = $(this).attr('data-id');
        $('#btnSaveformEditAssignment').val(id_assignment);
        $.ajax({
            type:'get',
            url: '/admin/assignment/'+ id_assignment +'/edit',
            success:function(res){
                let assignment = res.assignment;
                if(!res.error) {
                    $('#semester_edit').val(res.semesterName);
                    $('#name_user_edit').val(assignment.technicians_name);
                    $('#room_id_edit').val(assignment.room_id).select2().trigger('change');
                    $('#phone_edit').val(assignment.phone);
                    $('#time_assignment_edit').val(res.date);
                    $(editModal).modal('show');

                    //Gọi tới hàm lấy thời gian khi chọn thời gian trực
                    getTime('#time_assignment_edit',assignment.semester_id);
                }
            }
        });
    });

    /*Sự kiện khi người dùng click vào nút lưu từ form sửa dữ liệu*/
    $('#formEditAssignment').on('click','.btn-save',function(e){
        if(!$('#formEditAssignment').valid()) return false;
        e.preventDefault();
        let id = $('#btnSaveformEditAssignment').val();;
        let data = $('#formEditAssignment').serialize();
        $.ajax({
            type:'put',
            url:'/admin/assignment/update/'+ id,
            data: data,
            success:function(res){
                if(!res.error){
                    $('#listAssignment').DataTable().ajax.reload();
                    $('#editModal').modal('hide');
                    toastr.success(res.message, 'Thành công');
                }else{
                    toastr.error(res.message, 'Thất bại');
                }
            }
        });
    });

    /*Hàm tạo lịch
    * @param idTimeAssignment: id của thẻ muốn xuất hiện form lịch
    * @param idSemester: id học kỳ để lấy khoảng thời gian của học kỳ đó*/
    function getTime(idTimeAssignment, idSemester){
            $.ajax({
            dataType: '',
            type: 'POST',
            url: '/admin/assignment/getTimeOfSemester',
            data: {
                idSemester : idSemester
            },
            success: function (result)
            {
                $(idTimeAssignment).daterangepicker({
                    dayNamesMin: [ "1", "2", "3", "4", "5", "6", "7" ],
                    minDate: result.today,
                    maxDate: result.semester_end_date,
                    autoUpdateInput: false,
                    locale: { applyLabel: "Áp dụng", cancelLabel: "Thoát", format: 'DD/MM/YYYY',
                        daysOfWeek: ['CN','T2','T3','T4','T5','T6','T7'],
                        monthNames: ['Tháng 1 - ','Tháng 2 - ','Tháng 3 - ','Tháng 4 - ','Tháng 5 - ','Tháng 6 - ','Tháng 7 - ','Tháng 8 - ','Tháng 9 - ','Tháng 10 - ','Tháng 11 - ','Tháng 12 - '],
                        showDropdowns: true,
                    }
                });
                $(idTimeAssignment).on('apply.daterangepicker', function (ev, picker) {
                    $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
                });
            }
        });
    }

    /*
    * kiểm tra lỗi khi nhập không đúng định dạng thời gian*/
    jQuery.validator.addMethod("formatDateInput", function(value, element) {
        let pattern =/^([0-9]{2})\/([0-9]{2})\/([0-9]{4}) - ([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/;
        return pattern.test(value);
    }, "Vui lòng nhập đúng định dạng ngày !");

    /*Kiểm tra thời gian lịch trực tại mỗi phòng là duy nhất khi thêm mới*/
    jQuery.validator.addMethod("CheckUniqueAssignment", function(value, element) {
        var status = null;
        $.ajax({
            url: "/admin/assignment/check-time-assignment",
            type: "post",
            data: {
                room_id: function() {
                    return $( "#room_id_create" ).val();
                },
                time_assignment: function() {
                    return $( "#time_assignment_create" ).val();
                }
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

    /*Kiểm tra lỗi cho form thêm mới*/
    $('#formAddAssignment').validate({
            errorClass: 'errors',
            rules:{
                room_id_create:{
                    required:true,
                },
                name_user_create:{
                    required:true,
                },
                phone_create:{
                    required:true,
                },
                time_assignment_create:{
                    required:true,
                    "CheckUniqueAssignment": true,
                    "formatDateInput":true,
                }
            },
            messages:{
                room_id_create:{
                    required:"vui lòng chọn phòng máy!",
                },
                name_user_create:{
                    required:"Vui lòng nhập họ và tên!",
                },
                phone_create:{
                    required:"Vui lòng nhập số điện thoại",
                },
                time_assignment_create:{
                    required: "Vui lòng chọn thời gian trực",
                    "CheckUniqueAssignment": "Lịch trực này đã được sử dụng"
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
        });
    /*Kiểm tra thời gian lịch trực tại mỗi phòng là duy nhất khi sửa dữ liệu*/
    jQuery.validator.addMethod("CheckUniqueAssignmentEdit", function(value, element) {
        var status = null;
        $.ajax({
            url: "/admin/assignment/check-time-assignment",
            type: "post",
            data: {
                room_id: function() {
                    return $( "#room_id_edit" ).val();
                },
                time_assignment: function() {
                    return $( "#time_assignment_edit" ).val();
                },
                id: function() {
                    return $("#btnSaveformEditAssignment").val();
                },
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

    /*Kiểm tra lỗi cho form sửa dữ liệu*/
    $('#formEditAssignment').validate({
        errorClass: 'errors',
        rules:{
            room_id_edit:{
                required:true,
            },
            name_user_edit:{
                required:true,
            },
            phone_edit:{
                required:true,
            },
            time_assignment_edit:{
                required:true,
                "CheckUniqueAssignmentEdit": true,
                "formatDateInput":true,
            }
        },
        messages:{
            room_id_edit:{
                required:"vui lòng chọn phòng máy!",
            },
            name_user_edit:{
                required:"Vui lòng nhập họ và tên!",
            },
            phone_edit:{
                required:"Vui lòng nhập số điện thoại",
            },
            time_assignment_edit:{
                required: "Vui lòng chọn thời gian trực",
                "CheckUniqueAssignmentEdit": "Lịch trực này đã được sử dụng"
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
    });

});
