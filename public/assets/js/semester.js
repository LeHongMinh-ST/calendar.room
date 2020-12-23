jQuery.noConflict();

jQuery(document).ready(function($) {

    function css() {
        $('#listSemeter_wrapper ').addClass('main_table');
        $('#listSemeter_paginate ').addClass('pagination');
    }

    function css1() {
        $('#listWeek_wrapper ').addClass('main_table');
        $('#listWeek_paginate ').addClass('pagination');
    }

    $("#filterSemester").select2();
    $('#filterSchoolYear').select2();
    $('#add_school_year').select2({placeholder: "Vui lòng chọn năm học",});
    $('#add_semester').select2({placeholder: "Vui lòng chọn học kỳ",});
    $('#edit_school_year').select2();
    $('#edit_semester').select2();

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function dataTable(semester=null,school_year=null) {
        var table = $('#listSemeter').DataTable({
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
                url: '/admin/semester/getData',
                data:{
                    'semester': semester,
                    'school_year': school_year,
                }
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false},
                {data: 'school_year', name: 'school_year'},
                {data: 'semester', name: 'semester'},
                {data: 'number_weeks', name: 'number_weeks'},
                {data: 'semester_start_date', name: 'semester_start_date'},
                {data: 'semester_end_date', name: 'semester_end_date'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        });

        css();
    }
    dataTable();

    $('.filter').click(function(e){
        e.preventDefault();
        let semester = $('#filterSemester').val();
        let school_year = $('#filterSchoolYear').val();
        dataTable(semester,school_year);
    });

    $('#resetFilter').click(function(e){
        e.preventDefault();
        $('#filterSemester').select2().val(null).trigger('change');
        $('#filterSchoolYear').select2().val(null).trigger('change');
        dataTable();
    });

    $('#listSemeter').on('click', '.btn-delete', function (event) {
        event.preventDefault();
        let id = $(this).attr('data-id');

        Swal.fire({
            title: 'Xóa người dùng?',
            text: "Bạn có chắc chắn muốn xóa học kỳ này! Dữ liệu không thể khôi phục",
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
                    url: '/admin/semester/delete/' + id,
                    success: function (response) {
                        if(response.check_IsEmpty_Semester){
                            $('#listSemeter').DataTable().ajax.reload();
                            toastr.success('Bạn đã xóa thành công học kỳ ' + response.message, 'Xóa thành công');
                        }
                        else {
                            toastr.error('Bạn không thể xóa '+ response.message+ '. Vì học kỳ này đã được sử dụng', 'Xóa thất bại');
                        }
                    }
                });
            }
        })
    });

    //Create
    $('.btn-add').click(function (e) {
        e.preventDefault();
        $('#add_semester_start_date').prop("disabled",true);
        $('#add_number_weeks').val('');
        $('#add_semester_start_date').val('');
        $('#formAddSemester')[0].reset();
        $('#formAddSemester').validate().resetForm();
        $('#add_school_year').val(null).trigger('change');
        $('#add_semester').val(null).trigger('change');
        $('#addModal').modal('show');
        $.ajax
        ({
            type: 'get',
            url: '/admin/semester/create',
            data: '',
            success: function (response) {
                getTime($('#add_school_year').val(),'add_semester_start_date');
                changeYear('add_school_year','add_semester_start_date');
            },

        });
    });

    function getTime(school_year,id_semester_start_date) {
        let startYear = school_year.slice(0,4);
        let endYear = school_year.slice(7,11);
        let minYear = parseInt(startYear);
        let maxYear = parseInt(endYear);
        $('#'+id_semester_start_date).daterangepicker({
            singleDatePicker: true,
            minYear: minYear,
            maxYear: maxYear,
            showDropdowns: true,
            showWeekNumbers: false,
            autoUpdateInput: false,
            autoApply: true,
            minDate: '01/01/'+startYear,
            maxDate: '31/12/'+endYear,
            locale: {
                format: 'DD/MM/YYYY',
                applyLabel: "Áp dụng",
                cancelLabel: "Thoát",
                daysOfWeek: ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'],
                monthNames: ['Tháng 1 ', 'Tháng 2 ', 'Tháng 3 ', 'Tháng 4 ', 'Tháng 5 ', 'Tháng 6 ', 'Tháng 7 ', 'Tháng 8 ', 'Tháng 9 ', 'Tháng 10 ', 'Tháng 11 ', 'Tháng 12 '],
            }
        });
        $('#'+id_semester_start_date).on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/YYYY'));
        });
    }

    function changeYear(id_school_year, id_semester_start_date) {
        $("#"+id_school_year).change(function () {
            $('#'+id_semester_start_date).val('');
            let school_year = $(this).val();
            getTime(school_year,id_semester_start_date);
        });
    }

    $("#add_school_year").change(function () {
        if($(this).val() == ""){
            $('#add_semester_start_date').prop("disabled",true);
            $('#add_semester_start_date').val('');
            return;
        }
        else $('#add_semester_start_date').prop("disabled",false);
        let id_hk = $(this).val();
    });

    $('#btnSaveformAddSemester').click(function (e) {
        e.preventDefault();
        if(!$('#formAddSemester').valid()) return false;

        $.ajax({
            type: 'post',
            url: '/admin/semester/store',
            data: {
                semester: $('#add_semester').val(),
                number_weeks: $('#add_number_weeks').val(),
                school_year: $('#add_school_year').val(),
                semester_start_date: $('#add_semester_start_date').val(),
            },
            success: function(response) {
                if(response.error == false){
                    $('.error_time_semeser_create p').text('');
                    dataTable();
                    toastr.success(response.message,'Thành công');
                    $('#addModal').modal('hide');
                }else{
                    toastr.error(response.message,'Thất bại');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log("Lỗi");
            }

        });
    });

    //Edit
    $('#listSemeter').on('click','.btn-edit',function (e) {
        e.preventDefault();
        let id = $(this).attr('data-id');
        $('#formEditSemester')[0].reset();
        $('#formEditSemester').validate().resetForm();
        $('#editModal').modal('show');
        $.ajax({
            type:'get',
            url: '/admin/semester/'+ id +'/edit',
            success:function(response){
                let tg = String(response.yearNow);
                $('#edit_number_weeks').val(response.semester[0]['number_weeks']);
                $('#edit_semester_start_date').val(response.semester[0]['semester_start_date']);
                $('#btnSaveformEditSemester').val(id);
                $('#edit_semester').val(response.semester[0]['semester']).select2().trigger('change');
                $('#edit_school_year').append("<option value='" + response.semester[0]['school_year'] +' - '+ (++response.semester[0]['school_year']) + "' selected> "+ (--response.semester[0]['school_year']) +" - "+ (++response.semester[0]['school_year']) +"</option>");
                getTime($('#edit_school_year').val(),'edit_semester_start_date');
                changeYear('edit_school_year','edit_semester_start_date');
            },
            error: function (error) {
            }
        });
    });

    $('#formEditSemester').on('click','.btn-save',function (e) {
        e.preventDefault();
        let id = $('#btnSaveformEditSemester').val();
        if(!$('#formEditSemester').valid()){
            return false;
        }

        $.ajax({
            type: 'put',
            url: '/admin/semester/update/'+id,
            data: {
                semester: $('#edit_semester').val(),
                number_weeks: $('#edit_number_weeks').val(),
                school_year: $('#edit_school_year').val(),
                semester_start_date: $('#edit_semester_start_date').val(),
            },
            success: function(response) {
                if(response.error == false){
                    $('.error_time_semeser_edit p').text('');
                    dataTable();
                    toastr.success(response.message,'Thành công');
                    $('#editModal').modal('hide');
                }else{
                    toastr.error(response.message,'Thất bại');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log("Lỗi");
            }
        });
    });

    //Validate
    jQuery.validator.addMethod("formatDateInput", function(value, element) {
        return this.optional(element) || /^([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/.test(value) && !/\s/.test(value);
    }, "Vui lòng nhập đúng định dạng ngày !");


    jQuery.validator.addMethod("AddCheckUniqueTime", function(value, element) {
        var status = null;
        $.ajax({
            url:'/admin/semester/check-time-semester',
            type: 'post',
            data:{
                number_weeks: function () {
                    return $( "#add_number_weeks" ).val();
                },
                semester_start_date: function () {
                    return $( "#add_semester_start_date" ).val();
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
    },"Học ngày bắt đầu học kỳ này đã tồn tại!");

    jQuery.validator.addMethod("AddCheckUniqueSemester", function(value, element) {
        var status = null;
        $.ajax({
            url: "/admin/semester/check-semester-unique",
            type: "post",
            data: {
                semester: function() {
                    return $( "#add_semester" ).val();
                },
                school_year: function() {
                    return $( "#add_school_year" ).val();
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
    },"Học kỳ này đã tồn tại");

    $('#formAddSemester').validate({
        errorClass: 'errors',
        rules: {
            add_school_year:{
                required:true,
            },
            add_semester_start_date: {
                required: true,
                "AddCheckUniqueTime": true,
                "formatDateInput": true,
            },
            add_number_weeks: {
                required: true,
                number: true
            },
            add_semester:{
                required: true,
                'AddCheckUniqueSemester': true,
            }
        },
        messages: {
            add_school_year:{
                required:"Năm học không được để trống",
                AddCheckUniqueTime: 'Ngày bắt đầu học kỳ đã tồn tại'
            },
            add_semester_start_date: {
                    required :"Ngày bắt đầu không được để trống",
                },
            add_number_weeks: {
                required: "Số tuần không được để trống",
                number: "Số tuần phải là dạng số"
            },
            add_semester: {
                remote: "Học kỳ này đã tồn tại ",
                required: "Học kỳ không được bỏ trống"
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

    jQuery.validator.addMethod("EditCheckUniqueTime", function(value, element) {
        var status = null;
        $.ajax({
            url:'/admin/semester/check-time-semester',
            type: 'post',
            data:{
                number_weeks: function () {
                    return $( "#edit_number_weeks" ).val();
                },
                semester_start_date: function () {
                    return $( "#edit_semester_start_date" ).val();
                },
                id: function () {
                    return $("#btnSaveformEditSemester").val();
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
    },"Học ngày bắt đầu học kỳ này đã tồn tại trong học kỳ khác");

    jQuery.validator.addMethod("EditCheckUniqueSemester", function(value, element) {
        var status = null;
        $.ajax({
            url: "/admin/semester/check-semester-unique",
            type: "post",
            data: {
                semester: function() {
                    return $( "#edit_semester" ).val();
                },
                school_year: function() {
                    return $( "#edit_school_year" ).val();
                },
                id: function () {
                    return $("#btnSaveformEditSemester").val();
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
    },"Học kỳ này đã tồn tại");

    $('#formEditSemester').validate({
        errorClass: 'errors',
        rules: {
            edit_semester_start_date: {
                required: true,
                "EditCheckUniqueTime": true,
                "formatDateInput": true,
            },
            edit_number_weeks: {
                required: true,
                number: true
            },edit_semester: {
                required: true,
                "EditCheckUniqueSemester": true,
            },
        },
        messages: {
            edit_semester_start_date: {
                required :"Ngày bắt đầu không được để trống",
            },
            edit_number_weeks: {
                required: "Số tuần không được để trống",
                number: "Số tuần phải là dạng số"
            },
            edit_semester: {
                required:"Năm học không được để trống",
            }
        },
        errorPlacement: function(error, element) {
            let id = element.attr('id');
            if(element.hasClass('from-Edit-select2-input')){
                error.insertAfter($('#select2-'+id+'-container').parent(element));
            }else if(element.hasClass('datepicker')){
                error.insertAfter($('#'+id).parent(element));
            }else{
                error.insertAfter(element);
            }
        }
    });

});
