$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

var UserDataTable =  $('#table_user').DataTable({
    dom:'rtBp',
    processing: true,
    serverSide: true,
    searching: false,
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
        // {data: 'user_name', name: 'user_name', orderable: true, searchable: true},
        {data: 'full_name', name: 'full_name', orderable: true, searchable: true},
        {data: 'email', name: 'email', orderable: false, searchable: true},
        // {data: 'role_id', name: 'role_id', orderable: true, searchable: false},
        // {data: 'phone', name: 'phone', orderable: false, searchable: false},
        // {data: 'department_id', name: 'department_id', orderable: false, searchable: false},
        {data: 'is_active', name: 'is_active', orderable: false, searchable: false},
        // {data: 'action', name: 'action', orderable: false, searchable: false},
    ]
});


var schedulesDataTable = $('#table_schedules').DataTable({
    dom:'rtBp',
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
        "url": "/admin/dashboard/get-data",
    },
    columns: [
        {data: 'DT_RowIndex', searchable: false,class:'text-center'},
        {data: 'room_id', name: 'room_id', orderable: false, searchable: true},
        // {data: 'subject_id', name: 'subject_id', orderable: false, searchable: true},
        {data: 'subject_name', name: 'subject_name', orderable: false, searchable: true, class:'text-center'},
        // {data: 'teacher_name', name: 'teacher_name', orderable: false, searchable: false},
        // {data: 'amount_people', name: 'amount_people', orderable: false, searchable: false},
        {data: 'day', name: 'day', orderable: false, searchable: false},
        {data: 'session', name: 'session', orderable: false, searchable: false,class:'text-center'},
        {data: 'number_session', name: 'number_session', orderable: false, searchable: false,class:'text-center'},
        {data: 'week', name: 'week', orderable: false, searchable: false,class:'text-center'},
        {data: 'number_week', name: 'number_week', orderable: false, searchable: false,class:'text-center'},
        // {data: 'created_at', name: 'created_at', orderable: false, searchable: false,class:'text-center'},
        // {data: 'status', name: 'status', orderable: false, searchable: false},
        {data: 'action', name: 'action', orderable: false, searchable: false,class:'text-center'},
    ]
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

$('#table_schedules').on('click','.btn-edit',function (e) {
    e.preventDefault();
    let id = $(this).attr('data-id');
    console.log(id);
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
