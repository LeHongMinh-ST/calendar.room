$(document).ready(function () {

    const semester_id=  $('#filterSemester').val();

    $('#department').select2();
    $('#filterSemester').select2({
        language: {
            noResults: function() {
              return 'Chưa có học kì !';
            },
        },
    });
    $('#filterRoom').select2();
    $('#filterStatus').select2();

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
        $('#table_schedules').DataTable({
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
                "type": "post",
                "url": "/admin/schedules/getdata/",
                "data": {
                    "status": status,
                    "semester": semester,
                    "room": room,
                }
            },
            columns: [
                {data: 'checkbox',orderable: false, searchable: false,class:'text-center'},
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
                {data: 'created_at', name: 'created_at', orderable: false, searchable: false,class:'text-center'},
                {data: 'status', name: 'status', orderable: false, searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false,class:'text-center'},
            ]
        });
        css();
    }

    dataTable();

    $('#table_schedules').on('click', '.btn-delete', function (event) {
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
                            toastr.success(response.message, 'Thành công');
                        }else{
                            toastr.error(response.message, 'Thất bại');
                        }
                    }
                });
            }
        })
    })

    $('#formFilterSchedule').on('submit',function (event) {
        event.preventDefault();
        let status = $('#filterStatus').val();
        let semester = $('#filterSemester').val();
        let room = $('#filterRoom').val();

        if(status == 2)
            $('#op-delete').css('display','none');
        else
            $('#op-delete').css('display','block');

        if(status == 1 || status ==2){
            $('#op-comfirm').css('display','none');
        }else{
            $('#op-comfirm').css('display','block');

        }

        dataTable(status,semester,room);
    });

    $('#redoFilter').click(function () {
        dataTable();

        $('#filterStatus').val(0).trigger('change');
        $('#filterSemester').val(semester_id).trigger('change');
        $('#filterRoom').val(null).trigger('change');
    })

    $('#table_schedules').on('click','.btn-edit',function (e) {
        e.preventDefault();
        let id = $(this).attr('data-id');
        $.ajax({
            type:'put',
            url:'/admin/schedules/change-status/'+id,
            success:function(res){
                if(!res.error){
                    toastr.success(res.message, 'Thành công');
                    $('#table_schedules').DataTable().ajax.reload();
                }else{
                    toastr.error(res.message, 'Thất bại');
                }

            }
        });
    })

    $('#table_schedules').on('click','.btn-view',function (e) {
        e.preventDefault();
        let id = $(this).attr('data-id');

        $.ajax({
            type:'get',
            url:'/admin/schedules/show/'+id,
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
    });

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
                            url: '/admin/schedules/delete-select',
                            data: {
                                id: data
                            },
                            success: function (response) {
                                if (!response.error) {
                                    $('#table_schedules').DataTable().ajax.reload();
                                    toastr.success(response.message, 'Thành công');
                                } else {
                                    toastr.error(response.message, 'Thất bại');
                                }
                            }
                        });
                    }
                })
            }

            if(type == 'comfirm'){
                Swal.fire({
                    title: 'Xác nhận thòi khóa biểu',
                    text: "Bạn có chắc chắn muốn xác nhận yêu đã chọn!",
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
                            url: '/admin/schedules/comfirm',
                            data: {
                                id: data
                            },
                            success: function (response) {
                                if (!response.error) {
                                    $('#table_schedules').DataTable().ajax.reload();
                                    toastr.success(response.message, 'Thành công');
                                } else {
                                    toastr.error(response.message, 'Thất bại');
                                }
                            }
                        });
                    }
                })
            }
        }else{
            toastr.warning("Chưa có bản gi nào được chọn")
        }





        $('#actionSelectCheckbox').val('');
        $('.dt-checkboxes-all').prop('checked', false)
        $('.dt-checkboxes').prop('checked', false)
    })



})
