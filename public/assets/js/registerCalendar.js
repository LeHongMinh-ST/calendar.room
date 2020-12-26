$(document).ready(function () {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#subject').select2({
        placeholder: "Vui lòng chọn môn học",
    });

    $('#room').select2({
        placeholder: "Vui lòng chọn phòng học",
    });

    $('#weekCheck').select2({
        placeholder: "Vui lòng chọn tuần học",
        witdh: '100%',
        dropdownParent: $('#addEventModal'),
        language: {
            noResults: function () {
                return 'Chưa có môn học !';
            },
        },
        disabled: true,
    });

    jQuery.validator.addMethod("noSpace", function (value, element) {
        return value == '' || value.trim().length != 0;
    }, "Vui lòng không nhập khoảng trắng");

    jQuery.validator.addMethod("lessonMax", function (value, element) {
        let lessonStart = $('#lesson_start').val();
        if (lessonStart <= 5)
            return value <= 5 - lessonStart + 1;
        else if (lessonStart > 5 && lessonStart <= 10)
            return value <= 11 - lessonStart;
        else
            return value <= 14 - lessonStart;
    }, "Số tiết không hợp lệ! Vui lòng nhập lại số tiết !");

    jQuery.validator.addMethod("checkMaxWeek", function (value, element) {
        var status = null;

        $.ajax({
            url: '/calendar/check-max-week',
            type: 'post',
            data: {
                week_start: function () {
                    return value;
                }
            },
            async: false,
            success: (res) => {
                status = res;
            }
        });
        return status;
    }, "Tuần bắt đầu vượt quá số lượng tuần của học kì!");

    $('#formAddEventCalendar').validate({
        errorClass: 'errors',
        rules: {
            room: {
                required: true,
                'noSpace': true,
            },
            subject: {
                required: true,
                'noSpace': true,
            },
            lesson_start: {
                required: true,
                'noSpace': true,
                number: true,
                min: 1,
                max: 13,
            },
            week_start: {
                required: true,
                'noSpace': true,
                number: true,
                'checkMaxWeek': true,
                min: 1,
                remote: {
                    url: '/calendar/check-day-start',
                    type: 'post',
                    data: {
                        weekDay: function () {
                            return $('#weekDay').val();
                        },
                    }
                }
            },
            class: {
                required: true,
                'noSpace': true,
            },
            group: {
                required: true,
                'noSpace': true,
                number: true,
            },
            lesson_quantity: {
                required: true,
                'noSpace': true,
                min: 1,
                max: 5,
                number: true,
                'lessonMax': true,
            },
            week_quantity: {
                required: true,
                min: 1,
                number: true,
                remote: {
                    url: '/calendar/check-week-semester',
                    type: 'post',
                    data: {
                        week_start: function name(params) {
                            return $('#week_start').val();
                        },
                    }
                }
            },
            quantity: {
                required: true,
                'noSpace': true,
                number: true,
            },
            weekDay: {
                required: true,
                'noSpace': true,
            },


        },
        messages: {
            room: {
                required: 'Vui lòng chọn phòng máy!',
            },
            subject: {
                required: 'Vui lòng chọn môn học!',
            },
            lesson_start: {
                required: 'Vui lòng nhập tiết bắt đầu!',
                number: 'Tiết bắt đầu phải là số',
                min: 'Tiết bắt đầu phải lớn hơn 1',
                max: 'Tiết bắt đầu phải nhỏ hơn 13',
            },
            week_start: {
                required: 'Vui lòng nhập tuần bắt đầu!',
                number: 'Tuần bắt đầu phải là số',
                remote: 'Ngày bắt đầu và tuần bắt đầu phải lớn hơn thời gian hiện tại ! Vui lòng chọn ngày khác trong tuần hoặc tuần học khác !',
                min: 'Tuần bắt đầu phải lớn hơn 0!',
            },
            class: {
                required: 'Vui lòng nhập tên lớp!',
            },
            group: {
                required: 'Vui lòng nhập nhóm môn học!',
                number: 'Nhóm môn học phải là số',
            },
            lesson_quantity: {
                required: 'Vui lòng nhập số lượng tiết!',
                min: 'Số tiết phải lớn hơn 1',
                max: 'Số tiết phải nhỏ hơn 13',
                number: 'Số tiết phải là số',
            },
            week_quantity: {
                required: 'Vui lòng nhập số lượng tuần!',
                number: 'Số tuần phải là số',
                min: 'Tuần bắt đầu phải lớn hơn 0',
                remote: 'Số tuần vượt quá số lượng tuần của học kì!'
            },

            quantity: {
                required: 'Vui lòng nhập sĩ số lớp!',
                number: 'Sĩ số lớp phải là số',
            },
            weekDay: {
                required: 'Vui lòng chọn ngày trong tuần!',
            },
        },
        errorPlacement: function (error, element) {
            let id = element.attr('id');
            if (element.hasClass('select2-input')) {
                error.insertAfter($('#select2-' + id + '-container').parent(element));
            } else if (element.hasClass('datepicker')) {
                error.insertAfter($('#' + id).parent(element));
            } else {
                error.insertAfter(element);
            }
        }
    });

    $('#btnSubmitAddEventCalendar').click(function (e) {
        e.preventDefault();
        $('#formAddEventCalendar').valid();
        let formdata = $('#formAddEventCalendar').serialize()
        $.ajax({
            type: 'post',
            url: '/calendar/check-unique-schedules',
            data: formdata,
            success: function (res) {
                if (!res.status) {
                    toastr.error('Trùng lịch thời khóa biểu! Đã có môn học khác đăng kí trong thời gian này', 'Đăng kí không thành công');
                } else {
                    $('#formAddEventCalendar').submit();
                }
            }
        })
    });

    $('.week').change(function () {
        if ($('#week_quantity').val() != "" && $('#week_start').val() != "") {
            $('#weekCheck').empty().trigger("change");
            let data = [];
            let weekData = [];
            let week_quantity = $('#week_quantity').val();
            let week_start = $('#week_start').val();


            for (let i = week_start; i <= qualityWeekSemester; i++) {
                let option = {
                    id: i,
                    text: 'Tuần ' + i,
                }
                data.push(option);
            }

            for (let i = week_start; i <= (Number(week_quantity) + +Number(week_start) - 1); i++) {
                weekData.push(i);
            }


            $("#weekCheck").select2({
                data: data,
                maximumSelectionLength: week_quantity,
                language: {
                    maximumSelected: function (e) {
                        var t = "Bạn đã chọn đủ " + e.maximum + " tuần!";
                        e.maximum != 1;
                        return t + ' - Vui lòng chỉnh sửa lại số tuần để chọn thêm';
                    }
                }
            }).trigger('change');

            $('#weekCheck').val(null).trigger("change");


            $("#weekCheck").val(weekData).trigger('change');
            $("#weekCheck").prop("disabled", false);

        } else {
            $("#weekCheck").prop("disabled", true);
        }
    });


});
