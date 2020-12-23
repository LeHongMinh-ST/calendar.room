jQuery.noConflict();

jQuery(document).ready(function($) {
    function css1() {
        $('#listWeek_wrapper ').addClass('main_table');
        $('#listWeek_paginate ').addClass('pagination');
    }

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function dataTable1() {

        var semester_id=$('#data-id').val();
        console.log('/admin/semester/getWeek'+semester_id);
        var table = $('#listWeek').DataTable({
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
            // let id = $(this).attr('data-id'),
            processing: true,
            serverSide: true,
            searching: true,
            destroy: true,
            responsive: true,
            ajax: {
                type: 'get',
                url: '/admin/semester/getWeek/'+semester_id,
            },
            columns: [
                {
                    data: 'week',
                    name: 'week'
                },
                {
                    data: 'start_day',
                    name: 'start_day'
                },
                {
                    data: 'end_day',
                    name: 'end_day'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ]
        });
        css1();
    }
    dataTable1();

    $('#listWeek').on('click','.btn-edit',function(e){
        e.preventDefault();
        let week_id = $(this).attr('data-id');
        // console.log(week_id);
        $.ajax({
            type: 'get',
            url: '/admin/semester/getNote/'+week_id,
            success: function (response) {
                $('#note').val(response.note.note);
                $('.btn-save').attr('data-id',week_id);
                $("#addModal").modal('show');

            },
            error: function (jqXHR, textStatus, errorThrown) {
                //xử lý lỗi tại đây
            }
        })
        $("#addModal").modal('show');
    });

    $('.btn-save').click(function (e) {
        e.preventDefault();
        let week_id = $(this).attr('data-id');
        $.ajax({
            type: 'put',
            url: '/admin/semester/saveNote/'+week_id,
            data: {
                note: $('#note').val(),
            },
            success: function(response) {
                $('#listWeek').DataTable().ajax.reload();
                $('#addModal').modal('hide');
                toastr.success('Bạn đã thêm ghi chú thành công ' + response, 'Thêm ghi chú thành công');

            },

        })
    });

    $('#listWeek').on('click','.show-modal',function (e) {
        e.preventDefault();
        let week_id = $(this).attr('data-id');
        console.log(week_id);
        $.ajax({
            type: 'get',
            url: '/admin/semester/showWeek/'+week_id,
            success: function (response) {
                console.log(response.week.note);
                $('#week').text(response.week.week);
                $('#start_day').text(response.week.start_day);
                $('#end_day').text(response.week.end_day);
                $('#note1').text(response.week.note);
                $("#showModal").modal('show');
            },
            error: function (jqXHR, textStatus, errorThrown) {
            }
        })
        $("#showModal").modal('show');
    });
});
