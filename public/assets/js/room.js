jQuery.noConflict();

jQuery(document).ready(function($) {

    function css() {
        $('#listRoom_wrapper ').addClass('main_table');
        $('#listRoom_paginate ').addClass('pagination');
    }

    $("#add_subjects").select2();
    $("#edit_subjects").select2();

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function dataTable() {
        let id = $(this).attr('data-id');
        var table = $('#listRoom').DataTable({
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
                url: '/admin/room/getData',
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false},
                {data: 'room_id', name: 'room_id'},
                {data: 'name', name: 'name'},
                {data: 'computer_number', name: 'computer_number'},
                {data: 'address', name: 'address',orderable: false, searchable: false},
                {data: 'is_active', name: 'is_active',orderable: false, searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ]

        });
        css();
    }
    dataTable();


    $('#listRoom').on('change', '.active_switch', function () {
        let id = $(this).attr('data-id');
        $.ajax({
            type: 'put',
            url: '/admin/room/status/' + id,
            success: function (response) {
                if ($('#active_switch' + id).prop('checked')) {
                    $('#active_switch' + id).prop('checked', true);
                    toastr.success('Bạn đã mở thành công phòng máy '+response.room, 'Mở phòng máy');
                } else {
                    $('#active_switch' + id).prop('checked', false);
                    toastr.warning('Bạn đã tạm khóa phòng máy '+response.room, 'Tạm khóa phòng máy');
                }
            }
        })
    });

    $('#listRoom').on('click', '.btn-delete', function (event) {
        event.preventDefault();
        let id = $(this).attr('data-id');
        Swal.fire({
            title: 'Xóa phòng máy?',
            text: "Bạn có chắc chắn muốn xóa phòng máy này! Dữ liệu không thể khôi phục",
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
                    url: '/admin/room/delete/' + id,
                    success: function (response) {
                        if(response.check_IsEmpty_Room){
                            $('#listRoom').DataTable().ajax.reload();
                            toastr.success('Bạn đã xóa thành công phòng máy ' + response.room_id, 'Xóa thành công');
                        }
                        else {
                            toastr.error('Bạn không thể xóa phòng máy '+ response.room_id + '. Vì phòng máy này đã được sử dụng', 'Xóa thất bại');
                        }
                    }
                });
            }
        })
    });

    //Show
    $('#listRoom').on('click','.show-modal',function (e) {
        e.preventDefault();
        let room_id = $(this).attr('data-id');
        $.ajax({
            type: 'get',
            url: '/admin/room/show/'+room_id,
            success: function (response) {
                $('#room_id').text(response.room.room_id);
                $('#name').text(response.room.name);
                $('#computer_number').text(response.room.computer_number);
                $('#address').text(response.room.address);
                $('#subject').text(response.room.subject);
                $('#software').text(response.room.software);
                $("#showModal").modal('show');
            },
            error: function (jqXHR, textStatus, errorThrown) {
                //xử lý lỗi tại đây
            }
        })
    });

    //create
    $('.btn-add').click(function (e) {
        e.preventDefault();
        $('#formAddRoom')[0].reset();
        $('#formAddRoom').validate().resetForm();
        $('#addModal').modal('show');

        $.ajax
        ({
            type: 'get',
            url: '/admin/room/create',
            success: function (response) {
                if(response.error == false){
                    $("#add_subjects option").each(function() {
                        $(this).remove();
                    });
                    for (let i=0;i<response.subjects.length;i++) {
                        $('#add_subjects').append("<option value='" +response.subjects[i].name +"' >" + response.subjects[i].name + "</option>")
                    }
                }
                else{
                    //Lỗi lấy dữ liệu
                }
           },
        });
    });

    $('#btnSaveformAddRoom').click(function (e) {
        e.preventDefault();
        if(!$('#formAddRoom').valid()) return;
        $.ajax({
            type: 'post',
            url: "/admin/room/store",
            data: {
                room_id:  $( "#add_room_id" ).val(),
                name: $( "#add_name" ).val(),
                computer_number: $( "#add_computer_number" ).val(),
                subjects: $( "#add_subjects" ).val(),
                software: $( "#add_software" ).val(),
                address: $( "#add_address" ).val(),
            },
            success: function (response)
            {
                if(response.error == false){
                    dataTable();
                    toastr.success(response.message,'Thành công');
                    $('#addModal').modal('hide');
                }else{
                    toastr.error(response.error,'Thất bại');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
            }
        });
    });

    //Edit
    $('#listRoom').on('click','.btn-edit',function (e) {
        e.preventDefault();
        let id = $(this).attr('data-id');
        $('#editModal').modal('show');
        $.ajax({
            type: 'get',
            url: '/admin/room/' + id + '/edit',
            success:function(response){
                if(response.error == false){
                    $('#edit_room_id').val(response.room['room_id']);
                    $('#edit_name').val(response.room['name']);
                    $('#edit_software').val(response.room['software']);
                    $('#edit_computer_number').val(response.room['computer_number']);
                    $('#edit_address').val(response.room['address']);
                    $('#btnSaveformEditRoom').val(id);

                    $("#edit_subjects option").each(function() {
                        $(this).remove();
                    });
                    for (let value of response.subjects) {
                        if(response.listSubject.indexOf(value.name) > -1)
                            $('#edit_subjects').append("<option value='" +value.name +"' class='tags' selected >" + value.name + "</option>")
                        else
                            $('#edit_subjects').append("<option value='" +value.name +"' >" + value.name + "</option>")
                    }
                }
                else{
                    // Lỗi lấy dữ liệu
                }
            },
            error: function (error) {
            }
        });
    });

    $('#formEditRoom').on('click','.btn-save',function (e) {
        e.preventDefault();
        let id = $('#btnSaveformEditRoom').val();
        if($('#formEditRoom').valid() == false) return false;
        $.ajax({
            type: 'put',
            url: '/admin/room/update/'+id,
            data: {
                id: id,
                room_id:  $( "#edit_room_id" ).val(),
                name: $( "#edit_name" ).val(),
                computer_number: $( "#edit_computer_number" ).val(),
                subjects: $( "#edit_subjects" ).val(),
                software: $( "#edit_software" ).val(),
                address: $( "#edit_address" ).val(),
            },
            success: function (response)
            {
                if(response.error == false){
                    dataTable();
                    toastr.success(response.message,'Thành công');
                    $('#editModal').modal('hide');
                }else{
                    toastr.error(response.message,'Thất bại');
                }
            },
            error: function (error) {
            }
        });
    });

    //Validate
    // jQuery.validator.addMethod("lettersonlys", function(value, element) {
    //     return this.optional(element) || /^[a-zA-Z0-9]*$/.test(value) && !/\s/.test(value);
    // }, "Vui lòng nhập chuỗi không có kí tự đặc biệt, viết liền không dấu !");

    $('#formEditRoom').validate({
        errorClass: 'errors',
        rules: {
            edit_room_id: {
                required: true,
                // 'lettersonlys': true,
                remote:{
                    url: "/admin/room/check-room-id-unique",
                    type: "post",
                    data: {
                        room_id: function() {
                            return $( "#edit_room_id" ).val();
                        },
                        id: function() {
                            return $( "#btnSaveformEditRoom" ).val();
                        },
                    }
                }
            },
            edit_name: {
                required: true,
                remote:{
                    url: "/admin/room/check-name-room-unique",
                    type: "post",
                    data: {
                        name: function() {
                            return $( "#edit_name" ).val();
                        },
                        id: function() {
                            return $( "#btnSaveformEditRoom" ).val();
                        },
                    }
                }
            },
            edit_computer_number: {
                required: true,
                number: true
            },
            edit_address: {
                required: true,
            },
            edit_subjects: {
                required:true
            },
        },
        messages: {
            edit_room_id: {
                required :"Mã phòng không được để trống",
                remote: "Mã phòng này đã tồn tại"
            },
            edit_name: {
                required: "Tên phòng không được để trống",
                remote: "Tên phòng máy đã tồn tại"
            },
            edit_computer_number: {
                required: "Số lượng máy không được để trống ",
                number: "Số lượng máy phải là kiểu số"
            },
            edit_address: {
                required: "Địa chỉ không được để trống"
            },
            edit_subjects: {
                required: "Môn học không được để trống"
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

    $('#formAddRoom').validate({
        errorClass: 'errors',
        rules: {
            add_room_id: {
                required: true,
                // 'lettersonlys': true,
                remote:{
                    url: "/admin/room/check-room-id-unique",
                    type: "post",
                    data: {
                        room_id: function() {
                            return $( "#add_room_id" ).val();
                        }
                    }
                }
            },
            add_name: {
                required: true,
                remote:{
                    url: "/admin/room/check-name-room-unique",
                    type: "post",
                    data: {
                        name: function() {
                            return $( "#add_name" ).val();
                        }
                    }
                }
            },
            add_subjects: {
                required:true,
            },
            add_computer_number: {
                required: true,
                number: true
            },
            add_address: {
                required: true,
            },
        },
        messages: {
            add_room_id: {
                required :"Mã phòng không được để trống",
                remote: "Mã phòng này đã tồn tại"
            },
            add_name: {
                required: "Tên phòng không được để trống",
                remote: "Tên phòng máy đã tồn tại"
            },
            add_computer_number: {
                required: "Số lượng máy không được để trống ",
                number: "Số lượng máy phải là kiểu số"
            },
            add_address: {
                required: "Địa chỉ không được để trống"
            },
            add_subjects: {
                    required: "Môn học không được để trống"
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
