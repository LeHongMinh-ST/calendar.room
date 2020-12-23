// jQuery.noConflict();

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
    function dataTable(){
        var table= $('#listSubject').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            destroy: true,
            responsive: true,
            ajax: {
                url: '/admin/subject/getData',
            },
            columns: [
                { data: 'subject_id', name: 'subjects.subject_id' },
                { data: 'name', name: 'subjects.name' },
                { data: 'department', name: 'department' },
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        });
        css();
    }
    dataTable();

    $('.btn-add').click(function (e) {
        e.preventDefault();
        $('#addModal').modal('show');
        $.ajax({
            type: 'get',
            url: '/subject/create',
            success: function (response) {
                console.log(response.department[0].name);
                for(var i=0;i<response.department.length;i++){
                    $('#add_department_id').append("<option value=" + response.department[i].department_id + " selected>"+response.department[i].name+"</option>");
                    console.log("<option value=" + response.department[i].department_id + " selected>"+response.department[i].name+"</option>");
                }
            }

        })
    });
    $('.add-save').click(function (e) {
        e.preventDefault();
        console.log($('#add_name').val());
        $.ajax({
            type: 'POST',
            url: 'subject/store',
            data: {
                name: $('#add_name').val(),
                subject_id: $('#add_subject_id').val(),
                department_id: $('#add_department_id').val(),
            },
            success: function(response) {
                dataTable();
                swal({
                    title : "Tạo mới thành công",
                    icon : "success",
                    button : "Done",
                });
                $('#addModal').modal('hide');

            },
            error: function (jqXHR, textStatus, errorThrown) {
                swal({
                    title : "Tạo mới thất bại",
                    icon : "error",
                    type: 'error',
                    button : "Done",
                });
            }
        })
    });
    $('#listSubject').on('click','.show-modal',function (e) {
        e.preventDefault();
        var subject_id = $(this).attr('data-id');
        $("#showModal").modal('show');
        $.ajax({
            type: 'get',
            url: '/admin/subject/show/'+subject_id,
            success: function (response) {
                console.log(response);
                $('#show_name').text(response.subject.name);
                $('#show_subject_id').text(response.subject.subject_id);
                $('#show_department').text(response.department);
                $('#show_user_created').text(response.user_created);
                $('#show_created_at').text(response.subject.created_at);
                $('#show_updated_at').text(response.subject.updated_at);


            },
            error: function (jqXHR, textStatus, errorThrown) {
                //xử lý lỗi tại đây
            }
        })
        $("#showModal").modal('show');
    });
    $('#listSubject').on('click', '.btn-delete', function (event) {
        event.preventDefault();
        let id = $(this).attr('data-id');
        bootbox.confirm({
            title: "Xóa môn học",
            message: "Bạn có chắc chắn muốn xóa môn học này!",
            buttons: {
                cancel: {
                    label: '<i class="fa fa-times"></i> Trở lại'
                },
                confirm: {
                    label: '<i class="fa fa-check"></i> Xóa'
                }
            },
            callback: function (result) {
                if (result) {
                    $.ajax({
                        type: 'delete',
                        url: '/admin/subject/destroy/' + id,
                        success: function (response) {
                            $('#listSubject').DataTable().ajax.reload();
                            toastr.success('Bạn đã xóa thành công môn học ' + response, 'Xóa thành công');
                        }
                    })
                }
            }
        });
    })


});
