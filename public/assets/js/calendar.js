$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#subject').select2({
        placeholder: "Vui lòng chọn môn học",
        witdh:'100%',
        dropdownParent: $('#addEventModal'),
        language: {
            noResults: function() {
              return 'Chưa có môn học !';
            },
        },
    });

    $('#weekCheck').select2({
        placeholder: "Vui lòng chọn tuần học",
        witdh:'100%',
        dropdownParent: $('#addEventModal'),
        language: {
            noResults: function() {
                return 'Chưa có môn học !';
            },
        },
        disabled:true,
    });

    $('#room').select2({
        placeholder: "Vui lòng chọn phòng học",
        witdh:'100%',
        dropdownParent: $('#addEventModal'),
        language: {
            noResults: function() {
              return 'Chưa có phòng học !';
            },
        },
    });

    $('#edit_room').select2({
        placeholder: "Vui lòng chọn phòng học",
        witdh:'100%',
        dropdownParent: $('#editEventModal'),
        language: {
            noResults: function() {
              return 'Chưa có phòng học!';
            },
        },
    });

    $('#edit_subject').select2({
        placeholder: "Vui lòng chọn môn học",
        witdh:'100%',
        dropdownParent: $('#editEventModal'),
        language: {
            noResults: function() {
              return 'Chưa có môn học !';
            },
        },
    });

    $('#weekCheckEdit').select2({
        placeholder: "Vui lòng chọn tuần học",
        witdh:'100%',
        dropdownParent: $('#addEventModal'),
        language: {
            noResults: function() {
                return 'Chưa có môn học !';
            },
        },
    });

    $('#filterSemester').select2({
        language: {
            noResults: function() {
              return 'Chưa có học kì !';
            },
        },
    });
    $('#filterRoom').select2({
        language: {
            noResults: function() {
              return 'Chưa có phòng học !';
            },
        },
    });

    function getEvent() {
        $.ajax({
            type: 'get',
            url: '/get-schedules',
            success: function (response) {
                console.log(response)
                var calendarE1 = document.getElementById('calendar');

                var calendar = new FullCalendar.Calendar(calendarE1, {
                    locale: 'vi',
                    plugins: ['bootstrap', 'interaction', 'dayGrid', 'timeGrid','rrule'],
                    themeSystem: 'bootstrap',
                    header: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek'
                    },
                    weekNumbers: true,
                    weekNumbersWithinDays: true,
                    weekNumberCalculation: 'ISO',
                    defaultDate: Date.now(),
                    navLinks: true,
                    selectable: true,
                    selectMirror: true,
                    selectAllow: function(select) {
                        return moment().diff(select.start) <= 0
                    },
                    select: function (arg) {
                        if (arg.start.getDay() == 0)
                            $('#weekDay').val(8);
                        else
                            $('#weekDay').val(arg.start.getDay() + +1);

                        if(semester_id == now_id ){
                            if($('#formCreatSchedules')){
                                $('#formCreatSchedules').validate().resetForm();
                                $('#formCreatSchedules')[0].reset();
                                $.ajax({
                                    url:'/calendar/get-week-now',
                                    type:'post',
                                    data:{
                                        date:function () {
                                            return arg.startStr;
                                        }
                                    },
                                    success:function (res) {
                                        if(res.error != true){
                                            $('#weekNow').text(res.week);
                                            $('#addEventModal').modal('show');
                                        }else{
                                            toastr.warning('Tuần học không nằm trong học kì hiện tại!')
                                        }
                                    }
                                })

                            }
                        }
                    },
                    events: response,
                    eventClick: function (info) {
                        if (info.event.extendedProps.teacher_id == user_name || user_name == 'admin') {
                            let subject             = info.event.extendedProps.subject;
                            let className           = info.event.extendedProps.class;
                            let teacher_name        = info.event.extendedProps.teacher_name;
                            let status              = info.event.extendedProps.schedule.status;
                            let start_session       = info.event.extendedProps.schedule.session;
                            let number_session      = info.event.extendedProps.schedule.number_session;
                            let week                = info.event.extendedProps.schedule.week;
                            let week_session        = info.event.extendedProps.schedule.number_week;
                            let amount_people       = info.event.extendedProps.schedule.amount_people;
                            let id                  = info.event.extendedProps.schedule.id;
                            let group               = info.event.extendedProps.group;
                            let endWeek             = week + week_session - 1;
                            // let user_id = info.event.extendedProps.teacher_id;

                            if(status == 1 && (info.event.extendedProps.teacher_id == user_name || user_name == 'admin')){
                                $('#btnEditEvent').hide();
                            }else{
                                $('#btnEditEvent').show();
                            }

                            $('#detailSubject').text(subject);
                            $('#detailTeacher').text(teacher_name);
                            $('#detailClass').text(className);
                            $('#detailStartSession').text('Tiết '+start_session);
                            $('#detailStartWeek').text('Tuần '+week);
                            $('#detailEndWeek').text('Tuần '+ endWeek);
                            $('#detailNumberSession').text( number_session+ ' Tiết');
                            $('#detailAmountPpeople').text(amount_people + ' sinh viên');
                            $('#detailGroup').text(' Nhóm '+group);
                            $('#btnEditEvent').attr('data-id',id);
                            $('#detailEventlModal').modal('show');
                        }
                    }
                });

                calendar.render();
            }
        });
    }

    getEvent();

    jQuery.validator.addMethod("noSpace", function(value, element) {
        return value == '' || value.trim().length != 0;
    }, "Vui lòng không nhập khoảng trắng");

    jQuery.validator.addMethod("lessonMax", function(value, element) {
        let lessonStart = $('#lesson_start').val();
         if(lessonStart <=5)
            return value <= 5-lessonStart+1;
        else if(lessonStart >5 && lessonStart <=10)
            return value <=11-lessonStart;
        else
            return value <=14-lessonStart;
    }, "Số tiết không hợp lệ! Vui lòng nhập lại số tiết !");

    jQuery.validator.addMethod("lessonMaxUpdate", function(value, element) {
        let lessonStart = $('#edit_lesson_start').val();
         if(lessonStart <=5)
            return value <= 5-lessonStart+1;
        else if(lessonStart >5 && lessonStart <=10)
            return value <=11-lessonStart;
        else
            return value <=14-lessonStart;
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



    $('#formCreatSchedules').validate({
        errorClass: 'errors',
        rules:{
            room:{
                required: true,
                'noSpace': true,
            },
            subject:{
                required: true,
                'noSpace': true,
            },
            lesson_start:{
                required: true,
                'noSpace': true,
                number:true,
                min:1,
                max:13,
            },
            week_start:{
                required: true,
                'noSpace': true,
                number:true,
                'checkMaxWeek':true,
                min:1,
                remote:{
                    url:'/calendar/check-day-start',
                    type:'post',
                    data:{
                        weekDay: function() {
                            return $('#weekDay').val();
                        },
                    }
                }
            },
            class:{
                required: true,
                'noSpace': true,
            },
            group:{
                required: true,
                'noSpace': true,
                number:true,
            },
            lesson_quantity:{
                required: true,
                'noSpace': true,
                min:1,
                max:5,
                number:true,
                'lessonMax':true,
            },
            week_quantity:{
                required: true,
                'noSpace': true,
                number:true,
                min:1,
                remote:{
                    url:'/calendar/check-week-semester',
                    type:'post',
                    data:{
                        week_start: function name(params) {
                            return $('#week_start').val();
                        },
                    }
                }
            },
            quantity:{
                required: true,
                'noSpace': true,
                number:true,
            },
            weekDay:{
                required: true,
            },


        },
        messages:{
            room:{
                required: 'Vui lòng chọn phòng máy!',
            },
            subject:{
                required: 'Vui lòng chọn môn học!',
            },
            lesson_start:{
                required: 'Vui lòng nhập tiết bắt đầu!',
                number: 'Tiết bắt đầu phải là số',
                min: 'Tiết bắt đầu phải lớn hơn 1',
                max: 'Tiết bắt đầu phải nhỏ hơn 13',
            },
            week_start:{
                required: 'Vui lòng nhập tuần bắt đầu!',
                number:'Tuần bắt đầu phải là số',
                remote:'Ngày bắt đầu và tuần bắt đầu phải lớn hơn thời gian hiện tại ! Vui lòng chọn ngày khác trong tuần hoặc tuần học khác !',
            },
            class:{
                required: 'Vui lòng nhập tên lớp!',
            },
            group:{
                required: 'Vui lòng nhập nhóm môn học!',
                number: 'Nhóm môn học phải là số',
            },
            lesson_quantity:{
                required: 'Vui lòng nhập số lượng tiết!',
                min: 'Số tiết phải lớn hơn 1',
                max: 'Số tiết phải nhỏ hơn 5',
                number: 'Số tiết phải là số',
            },
            week_quantity:{
                required: 'Vui lòng nhập số lượng tuần!',
                number: 'Số tuần phải là số',
                min:'Số tuần phải lớn hơn 0',
                remote: 'Số tuần vượt quá số lượng tuần của học kì!'
            },

            quantity:{
                required: 'Vui lòng nhập sĩ số lớp!',
                number: 'Sĩ số lớp phải là số',
            },
            weekDay:{
                required: 'Vui lòng chọn ngày trong tuần!',
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
                'checkMaxWeek':true,
                min:1,
                remote:{
                    url:'/calendar/check-day-start',
                    type:'post',
                    data:{
                        weekDay: function() {
                            return $('#weekDay').val();
                        },
                        week_start: function() {
                            return $('#edit_week_start').val();
                        },
                    }
                }
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
                max:5,
                number:true,
                'lessonMaxUpdate':true,
            },
            edit_week_quantity:{
                required: true,
                'noSpace': true,
                number:true,
                min:1,
                remote:{
                    url:'/calendar/check-week-semester',
                    type:'post',
                    data:{
                        week_start: function () {
                            return $('#edit_week_start').val();
                        },
                        week_quantity: function(){
                            return $('#edit_week_quantity').val();
                        }
                    }
                }
            },
            edit_quantity:{
                required: true,
                'noSpace': true,
                number:true,
            },
            edit_weekDay:{
                required: true,
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
                remote:'Ngày bắt đầu và tuần bắt đầu phải lớn hơn thời gian hiện tại ! Vui lòng chọn ngày khác trong tuần hoặc tuần học khác !',
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
                max: 'Số tiết phải nhỏ hơn 5',
                number: 'Số tiết phải là số',
            },
            edit_week_quantity:{
                required: 'Vui lòng nhập số lượng tuần!',
                number: 'Số tuần phải là số',
                min:'Số tuần phải lớn hơn 0',
                remote: 'Số tuần vượt quá số lượng tuần của học kì!'
            },

            edit_quantity:{
                required: 'Vui lòng nhập sĩ số lớp!',
                number: 'Sĩ số lớp phải là số',
            },
            edit_weekDay:{
                required: 'Vui lòng chọn ngày trong tuần!',
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

    $('#btnSubmitAddEventCalendar').click(function (e) {
        e.preventDefault();
        $('#formCreatSchedules').valid();
        let formdata =  $('#formCreatSchedules').serialize();
        $.ajax({
            type:'post',
            url:'/calendar/check-unique-schedules',
            data: formdata,
            success:function(res){
                if(!res.status){
                    toastr.error('Trùng lịch thời khóa biểu! Đã có môn học khác đăng kí trong thời gian này','Đăng kí không thành công');
                }else{
                    $('#formCreatSchedules').submit();
                }
            }
        })
    });

    $('#btnEditEvent').click(function(){
        let id = $(this).attr('data-id');
        $.ajax({
            type:'get',
            url:'/calendar/'+id+'/edit',
            success:function(res){

                $('#edit_room').val(res.schedule.room_id).trigger('change');
                $('#edit_subject').val(res.schedule.subject_id).trigger('change');
                $('#edit_lesson_start').val(res.schedule.session);
                $('#edit_week_start').val(res.schedule.week);
                $('#edit_class').val(res.schedule.class);
                $('#edit_group').val(res.schedule.subject_group);
                $('#edit_lesson_quantity').val(res.schedule.number_session);
                $('#edit_week_quantity').val(res.schedule.number_week);
                $('#edit_quantity').val(res.schedule.amount_people);
                $('#edit_weekDay').val(res.schedule.day);
                $('#edit_note').val(res.schedule.note);
                getWeek(res.schedule.week_check.split(','));
                $('#formEditSchedules').attr('data-id',id);
                $('#detailEventlModal').modal('hide');
                $('#editEventModal').modal('show');
            }
        })
    })

    $('#formEditSchedules').on('submit',function(e){
        e.preventDefault();
        if(!$('#formEditSchedules').valid()) return false;
        let id      = $(this).attr('data-id');
        let data    = $('#formEditSchedules').serialize();

        $.ajax({
            type:'put',
            url:'/calendar/update/'+id,
            data: data,
            success: function(res){
                if(!res.error){
                    toastr.success(res.message);
                    $('#editEventModal').modal('hide');
                    setTimeout(function(){
                        window.location.replace("/calendar");
                    },5000);
                }else{
                    toastr.error(res.message);
                }
            }
        })
    });

    $('.week').change(function (){
        if($('#week_quantity').val()!="" && $('#week_start').val()!=""){
            $('#weekCheck').empty().trigger("change");
            let data = [];
            let weekData = [];
            let week_quantity = $('#week_quantity').val();
            let week_start = $('#week_start').val();


            for (let i = week_start ; i<= qualityWeekSemester; i++){
                let option = {
                    id:i,
                    text:'Tuần '+i,
                }
                data.push(option);
            }

            for (let i = week_start ; i<= (Number(week_quantity) + + Number(week_start)-1); i++){
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

        }else {
            $("#weekCheck").prop("disabled", true);
        }
    });

    $('.weekEdit').change(function (){
        if($('#edit_week_quantity').val()!="" && $('edit_#week_start').val()!=""){
            $('#edit_weekCheck').empty().trigger("change");
            let data = [];
            let weekData = [];
            let week_quantity = $('#edit_week_quantity').val();
            let week_start = $('#edit_week_start').val();


            for (let i = week_start ; i<= qualityWeekSemester; i++){
                let option = {
                    id:i,
                    text:'Tuần '+i,
                }
                data.push(option);
            }

            for (let i = week_start ; i<= (Number(week_quantity) + + Number(week_start)-1); i++){
                weekData.push(i);
            }


            $("#weekCheckEdit").select2({
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

            $('#weekCheckEdit').val(null).trigger("change");


            $("#weekCheckEdit").val(weekData).trigger('change');
            $("#weekCheckEdit").prop("disabled", false);

        }else {
            $("#weekCheckEdit").prop("disabled", true);
        }
    });


    function getWeek(week_check){
        let data = [];
        console.log(week_check);
        for (let i = week_check[0] ; i<= qualityWeekSemester; i++){
            let option = {
                id:i,
                text:'Tuần '+i,
            }
            data.push(option);
        }

        $("#weekCheckEdit").select2({
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

        $("#weekCheckEdit").val(week_check).trigger('change');
    }
});
