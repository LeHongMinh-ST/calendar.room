    $('#filterStatus').select2();
$('#filterRoom').select2();
$('summernote').summernote();

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
    function dataTable(handle=0,room_id=null){
        console.log(handle , room_id);
        var table= $('#listFeedback').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            destroy: true,
            responsive: true,
            ajax: {
                url: '/admin/feedback/getData/',
                type: 'POST',
                data:{
                    'handle': handle,
                    'room_id':room_id
                }
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
                {data: 'DT_RowIndex', searchable: false,"className": "text-center"},
                { data: 'room_id', name: 'feedback.room_id' },
                { data: 'created_at', name: 'feedback.created_at' },
                { data: 'user_create', name: 'user_create' },
                { data: 'status', name: 'status' },
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        });
        css();
    }
    dataTable();
    $('.filter').click(function(e){
        e.preventDefault();
        let handle = $('#filterStatus').val();
        let room_id = $('#filterRoom').val();
        console.log(handle , room_id);
        dataTable(handle,room_id);
    })
    $('#resetFilter').click(function(e){
        e.preventDefault();
        $('#filterStatus').select2().val(0).trigger('change');
        $('#filterRoom').select2().val(null).trigger('change');
        dataTable();
    })
    $('#listFeedback').on('click', '.btn-delete', function (event) {
        event.preventDefault();
        let id = $(this).attr('data-id');
        bootbox.confirm({
            title: "Xóa phản ánh",
            message: "Bạn có chắc chắn muốn gỡ phản ánh này!",
            buttons: {
                cancel: {
                    label: '<i class="fa fa-times"></i> Trở lại'
                },
                confirm: {
                    label: '<i class="fa fa-check"></i> Gỡ'
                }
            },
            callback: function (result) {
                if (result) {
                    $.ajax({
                        type: 'delete',
                        url: '/admin/feedback/destroy/' + id,
                        success: function (response) {
                            $('#listFeedback').DataTable().ajax.reload();
                            toastr.success('Bạn đã gỡ thành công phản ánh ', 'Gỡ thành công');
                        }
                    })
                }
            }
        });
    });
    $('#listFeedback').on('click', '.btn-show', function (e) {
        e.preventDefault();
        let id = $(this).attr('data-id');
        $.ajax({
            type: 'get',
            url: '/admin/feedback/show/'+id,
            success: function (response) {
                $('#showModal').modal('show');
                $('.room').html('<b>Phòng: </b>' + response.room);
                $('.content').html('<b>Nội dung: </b><br>');
                $('#content_show').html(response.feedback.content);
                $('.user').html('<b>Người gửi: </b>'+response.user);
                $('.date').html('<b>Ngày phản ánh: </b>'+response.time);
                $('#button').html(response.button);

            }
        })
    })

    $('#showModal').on('click', '.btn-handle', function (e) {
        e.preventDefault();
        let idFeedback= $(this).attr('data-id');
        $('#showModal').modal('hide');
        $('#validate-handle').text('');
        $('#hahdle').modal('show');
        $('#create_handle')[0].reset();
        $('#feedback_id').val(idFeedback);
    } )
    $('#create_handle').on('click', '.add-handle', function () {
        var content = document.getElementById('description').value;
        console.log('content: '+content.length);
        if(content.length == 0){
            $('#validate-handle').text('Vui lòng nhập nội dung xử lí');
        }else{
            $.ajax({
                type: 'post',
                url: '/admin/feedback/storehandle',
                data: {
                    'feedback_id': $('#feedback_id').val(),
                    'description': $("#description").summernote("code")
                },
                success: function (result) {
                    if (result.success) {
                        $('#listFeedback').DataTable().ajax.reload();
                        toastr.success('xử lí phản ánh', 'Thành công');
                        $('#hahdle').modal('hide');

                    } else {
                        toastr.error('{{$errors->first("description") }}', 'Tạo mới thất bại')
                    }
                },
                errors: function (result) {
                }
            })

        }
    })
    $('#showModal').on('click', '.btn-show_handle', function (e) {
        e.preventDefault();
        let id = $(this).attr('data-id');
        $.ajax({
            type: 'get',
            url: '/admin/feedback/show/handle/'+id,
            success: function (response) {
                $('#showHandle').modal('show');
                $('.content').html('<b><i>Nội dung: </i></b><br>');
                $('#content_show').val(response.handle_feedback.description);
                $('.user').html('<b><i>Người xử lí: </i></b>'+response.user);
                $('.date').html('<b><i>ngày xử lí: </i></b>'+response.time);

            }
        })
    })
    $('#add-feedback').click(function () {
        $('#addModal').modal('show');
    })
    $('#submit_create').click( function () {
        if (valid()){
            $.ajax({
                type: 'post',
                url: '/admin/feedback/store',
                data: {
                    'room_id': $('#select_room').val(),
                    'content': $("#content").summernote("code")
                },
                success: function (result) {
                    if(result.success == true ){
                        $('#addModal').modal('hide');
                        $('#listFeedback').DataTable().ajax.reload();
                        toastr.success('Gửi thành công một phản ánh', 'Thành công');
                    }


                },
                errors: function (result) {
                }
            })

        }
    })



});

function valid() {
    var content = document.getElementById('content').value;
    if(content.length<11){
        toastr.error('Nội dung phản ánh không được để trống', 'Tạo mới thất bại');
        return false;
    }else if (content.length<=17){
        toastr.error('Nội dung phản ánh không được ngắn dưới 10 kí tự', 'Tạo mới thất bại');
        return false;
    }
    return true;
}

