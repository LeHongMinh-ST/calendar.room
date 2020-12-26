$(document).ready(function(){
    const semester_id=  $('#filterSemester').val();

    // $('#department').select2();
    $('#filterSemester').select2();
    $('#filterRoom').select2();
    $('#filterStatus').select2();

    $('#edit_subject').select2({
        placeholder: "Vui lòng chọn môn học",
        dropdownParent: $('#editEventModal')
    });

    $('#edit_room').select2({
        placeholder: "Vui lòng chọn phòng học",
        dropdownParent: $('#editEventModal')
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function css() {
        // $('#table_user_wrapper').addClass('main_table');
        $('#table_user_paginate').addClass('pagination');
    }

    function dataTable(status=0,semester=null,room=null) {
        $('#tableRegisterSchedules').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            destroy: true,
            responsive: true,
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
                "type": "post",
                "url": "/calendar/register-schedules/getdata",
                "data": {
                    "status": status,
                    "semester": semester,
                    "room": room,
                }
            },
                // 'columnDefs': [
                //     {
                //         targets: 0,
                //         'checkboxes': {
                //             'selectRow': true,
                //         },
                //     }
                // ],
            'select': {
                'style': 'multi',selector: 'td:first-child'

            },
            // 'order': [[1, 'asc']],
            columns: [
                {data: 'checkbox', orderable: false,searchable: false,class:'text-center'},
                {data: 'room_id', name: 'room_id', orderable: false, searchable: true},
                {data: 'subject_id', name: 'subject_id', orderable: false, searchable: true},
                {data: 'subject_name', name: 'subject_name', orderable: false, searchable: true, class:'text-center'},
                {data: 'teacher_name', name: 'teacher_name', orderable: false, searchable: false},
                {data: 'amount_people', name: 'amount_people', orderable: false, searchable: false},
                {data: 'day', name: 'day', orderable: false, searchable: false},
                {data: 'session', name: 'session', orderable: false, searchable: false,class:'text-center'},
                {data: 'number_session', name: 'number_session', orderable: false, searchable: false,class:'text-center'},
                {data: 'week', name: 'week', orderable: false, searchable: false,class:'text-center'},
                {data: 'number_week', name: 'number_week', orderable: false, searchable: false,class:'text-center'},
                {data: 'created_at', name: 'created_at', orderable: false, searchable: true,class:'text-center'},
                {data: 'status', name: 'status', orderable: false, searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false,class:'text-center',witdh:'15%'},
            ]
        });
        css();
        $('.dt-checkboxes-all').prop('checked', false)
    }

    dataTable();




    $('#formFilterSchedule').on('submit',function (event) {
        event.preventDefault();
        let status = $('#filterStatus').val();
        let semester = $('#filterSemester').val();
        let room = $('#filterRoom').val();


        if(status == 2)
            $('#op-delete').css('display','none');
        else
            $('#op-delete').css('display','block');

        dataTable(status,semester,room);

    });

    $('#redoFilter').click(function () {
        dataTable();

        $('#filterStatus').val(0).trigger('change');
        $('#filterSemester').val(semester_id).trigger('change');
        $('#filterRoom').val(null).trigger('change');
    })


    jQuery.validator.addMethod("noSpace", function(value, element) {
        return value == '' || value.trim().length != 0;
    }, "Vui lòng không nhập khoảng trắng");

    jQuery.validator.addMethod("lessonMaxUpdate", function(value, element) {
        let lessonStart = $('#edit_lesson_start').val();
        if(lessonStart <=1)
            return value < 6;
        else if(lessonStart >5 && lessonStart <=10)
            return value <11 && value>5;
        else
            return value <14 && value>10;
    }, "Số tiết không hợp lệ! Vui lòng nhập lại số tiết !");

    jQuery.validator.addMethod("checkMaxWeek", function(value, element) {
        var status = null;

        $.ajax({
            url:'/calendar/check-max-week',
            type: 'post',
            data:{
                week_start: function () {
                    return value;
                }
            },
            async: false,
            success: (res) => {
                status =  res;
            }
        });
        return status;
    },"Tuần bắt đầu vượt quá số lượng tuần của học kì!");

    $('#formEditSchedules').validate({
        errorClass: 'errors',
        rules:{
            edit_room:{
                required: true,
                'noSpace': true,
            },
            edit_subject:{
                required: true,
                'noSpace': true,
            },
            edit_lesson_start:{
                required: true,
                'noSpace': true,
                number:true,
                min:1,
                max:13,
            },
            edit_week_start:{
                required: true,
                'noSpace': true,
                number:true,
            },
            edit_class:{
                required: true,
                'noSpace': true,
            },
            edit_group:{
                required: true,
                'noSpace': true,
                number:true,
            },
            edit_lesson_quantity:{
                required: true,
                'noSpace': true,
                min:1,
                max:13,
                number:true,
            },
            edit_week_quantity:{
                required: true,
                'noSpace': true,
                number:true,
            },
            edit_quantity:{
                required: true,
                'noSpace': true,
                number:true,
            },
            edit_weekDay:{
                required: true,
                'noSpace': true,
            },


        },
        messages:{
            edit_room:{
                required: 'Vui lòng chọn phòng máy!',
            },
            edit_subject:{
                required: 'Vui lòng chọn môn học!',
            },
            edit_lesson_start:{
                required: 'Vui lòng nhập tiết bắt đầu!',
                number: 'Tiết bắt đầu phải là số',
                min: 'Tiết bắt đầu phải lớn hơn 1',
                max: 'Tiết bắt đầu phải nhỏ hơn 13',
            },
            edit_week_start:{
                required: 'Vui lòng nhập tuần bắt đầu!',
                number:'Tuần bắt đầu phải là số',
            },
            edit_class:{
                required: 'Vui lòng nhập tên lớp!',
            },
            edit_group:{
                required: 'Vui lòng nhập nhóm môn học!',
                number: 'Nhóm môn học phải là số',
            },
            edit_lesson_quantity:{
                required: 'Vui lòng nhập số lượng tiết!',
                min: 'Số tiết phải lớn hơn 1',
                max: 'Số tiết phải nhỏ hơn 13',
                number: 'Số tiết phải là số',
            },
            edit_week_quantity:{
                required: 'Vui lòng nhập số lượng tuần!',
                number: 'Số tuần phải là số',
            },

            edit_quantity:{
                required: 'Vui lòng nhập sĩ số lớp!',
                number: 'Sĩ số lớp phải là số',
            },
            edit_weekDay:{
                required: 'Vui lòng chọn ngày trong tuần!',
            },
        }
    });

    $('#tableRegisterSchedules').on('click','.btn-view',function (e) {
        e.preventDefault();
        let id = $(this).attr('data-id');
        $.ajax({
            type:'get',
            url:'/calendar/register-schedules/show/'+id,
            success:function(res){
                let schedule = res.schedule;
                if(!res.error){
                    let end_week = schedule.week + schedule.number_week-1;
                    $('#detailSubject').text(schedule.subject);
                    $('#detailTeacher').text(schedule.teacher_name);
                    $('#detailClass').text(schedule.class);
                    $('#detailStartSession').text('Tiết '+schedule.session);
                    $('#detailStartWeek').text('Tuần '+schedule.week);
                    $('#detailEndWeek').text('Tuần '+ end_week);
                    $('#detailNumberSession').text( schedule.number_session+ ' Tiết');
                    $('#detailAmountPpeople').text(schedule.amount_people + ' sinh viên');
                    $('#detailGroup').text(' Nhóm '+schedule.subject_group);

                    $('#detailEventlModal').modal('show');
                }
            }
        });
    })

    $('#tableRegisterSchedules').on('click','.btn-edit',function (e) {
        e.preventDefault();
        let id = $(this).attr('data-id');
        $.ajax({
            type:'get',
            url:'/calendar/register-schedules/'+id+'/edit',
            success:function(res){
                let schedule = res.schedule;
                if(!res.error){
                    $('#edit_room').select2().val(schedule.room_id).trigger('change');
                    $('#edit_subject').select2().val(schedule.subject_id).trigger('change');
                    $('#edit_lesson_start').val(schedule.session);
                    $('#edit_week_start').val(schedule.week);
                    $('#edit_class').val(schedule.class);
                    $('#edit_group').val(schedule.subject_group);
                    $('#edit_lesson_quantity').val(schedule.session);
                    $('#edit_week_quantity').val(schedule.number_week);
                    $('#edit_quantity').val(schedule.amount_people);
                    $('#edit_weekDay').val(schedule.day);
                    $('#edit_note').val(schedule.note);
                    $('#formEditSchedules').attr('data-id',id);
                    $('#editEventModal').modal('show');
                }
            }
        });
    })

    $('#formEditSchedules').on('submit',function (e) {
        e.preventDefault();
        if(!$('#formEditSchedules').valid()) return false;
        let id = $(this).attr('data-id');
        let formdata =  $('#formEditSchedules').serialize();
        $.ajax({
            type:'put',
            url:'/calendar/register-schedules/update/'+id,
            data:formdata,
            success:function (res) {
                if(!res.error){
                    $('#tableRegisterSchedules').DataTable().ajax.reload();
                    $('#editEventModal').modal('hide');
                    toastr.success(res.message, 'Thành công');
                }else{
                    toastr.error(res.message, 'Thất bại');
                }
            }
        });
    });

    $('#btnSubmitEditEventCalendar').click(function (e) {
        e.preventDefault();
        $('#formEditSchedules').valid();
        let formdata =  $('#formEditSchedules').serialize();
        let id = $(this).attr('data-id');

        $.ajax({
            type:'post',
            url:'/calendar/register-schedules/check-unique-update-schedules/'+id,
            data: formdata,
            success:function(res){
                if(!res.status){
                    toastr.error('Trùng lịch thời khóa biểu! Đã có môn học khác đăng kí trong thời gian này','Cập nhật không thành công');
                }else{
                    $('#formEditSchedules').submit();
                }
            }
        })
    });

    $('#tableRegisterSchedules').on('click', '.btn-delete', function (event) {
        event.preventDefault();
        let id = $(this).attr('data-id');
        Swal.fire({
            title: 'Xóa thòi khóa biểu',
            text: "Bạn có chắc chắn muốn xóa yêu  này! Dữ liệu không thể khôi phục",
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
                    url: '/admin/schedules/delete/' + id,
                    success: function (response) {
                        if(!response.error){
                            $('#table_schedules').DataTable().ajax.reload();
                            toastr.success(response.message);
                        }else{
                            toastr.error(response.message);
                        }
                    }
                });
            }
        })
    })

    $('.dt-checkboxes-all').change(function (){
        if($('.dt-checkboxes-all:checked').length > 0){
            $('.dt-checkboxes, input:checkbox').not(this).prop('checked', this.checked)
        }else {
            $('.dt-checkboxes, input:checkbox').not(this).prop('checked', this.checked,false)
        }
    })



    $('#tableRegisterSchedules').on('change','.dt-checkboxes',function (){
        let length = $('.dt-checkboxes').length
        let rowcolumn = $('.dt-checkboxes:checked')
        if(this.checked == false) {
            $('.dt-checkboxes-all:checked').prop('checked', this.checked,false)
        }

        if(rowcolumn.length == length){
            $('.dt-checkboxes-all').prop('checked', this.checked)
        }
    });
    $('#actionSelectCheckbox').change(function (){
        let type = $(this).val();
        console.log(type)
        let data = [];
        $.each($('.dt-checkboxes:checked'),function (){
            data.push( $(this).attr('data-id'));
        })

        if($('.dt-checkboxes:checked').length > 0){
            if(type == 'delete') {

                Swal.fire({
                    title: 'Xóa thòi khóa biểu',
                    text: "Bạn có chắc chắn muốn xóa yêu đã chọn! Dữ liệu không thể khôi phục",
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
                            url: '/calendar/register-schedules/delete-select',
                            data: {
                                id: data
                            },
                            success: function (response) {
                                if (!response.error) {
                                    $('#tableRegisterSchedules').DataTable().ajax.reload();
                                    toastr.success(response.message, 'Thành công');
                                } else {
                                    toastr.error(response.message, 'Thất bại');
                                }
                            }
                        });
                    }
                })
            }

            if(type == 'export'){
                toastr.warning("Chức năng đang được phát triển!")
            }
        }else{
            toastr.warning("Chưa có bản gi nào được chọn")
        }





        $('#actionSelectCheckbox').val('');
        $('.dt-checkboxes-all').prop('checked', false)
    })


})
