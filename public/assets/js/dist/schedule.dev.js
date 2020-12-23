"use strict";

$(document).ready(function () {
  var semester_id = $('#filterSemester').val();
  $('#department').select2();
  $('#filterSemester').select2();
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

  function dataTable() {
    var status = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 0;
    var semester = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
    var room = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;
    $('#table_schedules').DataTable({
      processing: true,
      serverSide: true,
      searching: true,
      destroy: true,
      // responsive: true,
      language: {
        sProcessing: "Đang xử lý...",
        sLengthMenu: "Xem _MENU_ mục",
        sZeroRecords: "Không tìm thấy dòng nào phù hợp",
        sInfo: "Đang xem _START_ đến _END_ trong tổng số _TOTAL_ mục",
        sInfoEmpty: "Đang xem 0 đến 0 trong tổng số 0 mục",
        sInfoFiltered: "(được lọc từ _MAX_ mục)",
        sSearch: 'Tìm kiếm',
        lengthMenu: '_MENU_ bản ghi/trang',
        oPaginate: {
          "sFirst": "Đầu",
          "sPrevious": "Trước",
          "sNext": "Tiếp",
          "sLast": "Cuối"
        }
      },
      ajax: {
        "type": "post",
        "url": "/admin/schedules/getdata/",
        "data": {
          "status": status,
          "semester": semester,
          "room": room
        }
      },
      columns: [{
        data: 'DT_RowIndex',
        searchable: false,
        "class": 'text-center'
      }, {
        data: 'room_id',
        name: 'room_id',
        orderable: false,
        searchable: true
      }, {
        data: 'subject_id',
        name: 'subject_id',
        orderable: false,
        searchable: true
      }, {
        data: 'subject_name',
        name: 'subject_name',
        orderable: false,
        searchable: true,
        "class": 'text-center'
      }, {
        data: 'teacher_name',
        name: 'teacher_name',
        orderable: false,
        searchable: false
      }, {
        data: 'amount_people',
        name: 'amount_people',
        orderable: false,
        searchable: false
      }, {
        data: 'day',
        name: 'day',
        orderable: false,
        searchable: false
      }, {
        data: 'session',
        name: 'session',
        orderable: false,
        searchable: false,
        "class": 'text-center'
      }, {
        data: 'number_session',
        name: 'number_session',
        orderable: false,
        searchable: false,
        "class": 'text-center'
      }, {
        data: 'week',
        name: 'week',
        orderable: false,
        searchable: false,
        "class": 'text-center'
      }, {
        data: 'number_week',
        name: 'number_week',
        orderable: false,
        searchable: false,
        "class": 'text-center'
      }, {
        data: 'created_at',
        name: 'created_at',
        orderable: false,
        searchable: false,
        "class": 'text-center'
      }, {
        data: 'status',
        name: 'status',
        orderable: false,
        searchable: false
      }, {
        data: 'action',
        name: 'action',
        orderable: false,
        searchable: false,
        "class": 'text-center'
      }]
    });
    css();
  }

  dataTable();
  $('#table_schedules').on('click', '.btn-delete', function (event) {
    event.preventDefault();
    var id = $(this).attr('data-id');
    bootbox.confirm({
      title: "Xóa yêu cầu đăng kí",
      message: "Bạn có chắc chắn muốn xóa yêu cầu này!",
      buttons: {
        cancel: {
          label: '<i class="fa fa-times"></i> Trở lại'
        },
        confirm: {
          label: '<i class="fa fa-check"></i> Xóa'
        }
      },
      callback: function callback(result) {
        if (result) {
          $.ajax({
            type: 'delete',
            url: '/admin/schedules/delete/' + id,
            success: function success(response) {
              if (!response.error) {
                $('#table_schedules').DataTable().ajax.reload();
                toastr.success(response.message, 'Thành công');
              } else {
                toastr.error(response.message, 'Thất bại');
              }
            }
          });
        }
      }
    });
  });
  $('#formFilterSchedule').on('submit', function (event) {
    event.preventDefault();
    var status = $('#filterStatus').val();
    var semester = $('#filterSemester').val();
    var room = $('#filterRoom').val();
    dataTable(status, semester, room);
  });
  $('#redoFilter').click(function () {
    dataTable();
    $('#filterStatus').val(0).trigger('change');
    $('#filterSemester').val(semester_id).trigger('change');
    $('#filterRoom').val(null).trigger('change');
  });
  $('#table_schedules').on('click', '.btn-edit', function (e) {
    e.preventDefault();
    var id = $(this).attr('data-id');
    console.log(id);
    $.ajax({
      type: 'put',
      url: '/admin/schedules/change-status/' + id,
      success: function success(res) {
        if (!res.error) {
          toastr.success(res.message, 'Thành công');
          $('#table_schedules').DataTable().ajax.reload();
        } else {
          toastr.error(res.message, 'Thất bại');
        }
      }
    });
  });
  $('#table_schedules').on('click', '.btn-view', function (e) {
    e.preventDefault();
    var id = $(this).attr('data-id');
    $.ajax({
      type: 'get',
      url: '/admin/schedules/show/' + id,
      success: function success(res) {
        var schedule = res.schedule;

        if (!res.error) {
          var end_week = schedule.week + schedule.number_week - 1;
          $('#detailSubject').text(schedule.subject);
          $('#detailTeacher').text(schedule.teacher_name);
          $('#detailClass').text(schedule["class"]);
          $('#detailStartSession').text('Tiết ' + schedule.session);
          $('#detailStartWeek').text('Tuần ' + schedule.week);
          $('#detailEndWeek').text('Tuần ' + end_week);
          $('#detailNumberSession').text(schedule.number_session + ' Tiết');
          $('#detailAmountPpeople').text(schedule.amount_people + ' sinh viên');
          $('#detailGroup').text(' Nhóm ' + schedule.subject_group);
          $('#detailEventlModal').modal('show');
        }
      }
    });
  });
});