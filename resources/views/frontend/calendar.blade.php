@extends('layouts.master')

@section('title')
    {{__('Calendar')}}
@endsection
@section('css')
    <link href='{{asset('assets/fullcalendar')}}/packages/core/main.css' rel='stylesheet'/>
    <link href='{{asset('assets/fullcalendar')}}/packages/daygrid/main.css' rel='stylesheet'/>
    <link href='{{asset('assets/fullcalendar')}}/packages/timegrid/main.css' rel='stylesheet'/>
    <link href='{{asset('assets/fullcalendar')}}/packages/bootstrap/main.css' rel='stylesheet'/>
    <style>
        .fc-content{
            text-align: left;
        }
        .required {
            color: red;
        }

        .select2-selection__rendered {
            padding-top: 6px;
        }
        #formExcel{
            position: relative;
            margin: 0 auto;
            width: 500px;
            height: 200px;
            border: 4px dashed #7b7373;
        }

        #formExcel .form-group p{
            width: 100%;
            height: 100%;
            text-align: center;
            line-height: 200px;
            color: #7b7373;
            font-family: Arial;
        }
        #fileExcel{
            position: absolute;
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            outline: none;
            opacity: 0;
        }
        #uploadEx{
            margin: 0;
            color: #fff;
            background: #16a085;
            border: none;
            width: 100%;
            height: 35px;
            border-radius: 4px;
            border-bottom: 4px solid #117A60;
            transition: all .2s ease;
            outline: none;
            display: none;
        }
        #uploadEx:hover{
            background: #149174;
            color: #0C5645;
        }
        #uploadEx:active{
            border:0;
        }
    </style>

    <style type="text/css">
        @keyframes ldio-w61dkjf0pdd {
            0% { opacity: 1 }
            100% { opacity: 0 }
        }
        .ldio-w61dkjf0pdd div {
            left: 94px;
            top: 48px;
            position: absolute;
            animation: ldio-w61dkjf0pdd linear 1s infinite;
            background: #93dbe9;
            width: 12px;
            height: 24px;
            border-radius: 6px / 12px;
            transform-origin: 6px 52px;
        }.ldio-w61dkjf0pdd div:nth-child(1) {
             transform: rotate(0deg);
             animation-delay: -0.9166666666666666s;
             background: #93dbe9;
         }.ldio-w61dkjf0pdd div:nth-child(2) {
              transform: rotate(30deg);
              animation-delay: -0.8333333333333334s;
              background: #93dbe9;
          }.ldio-w61dkjf0pdd div:nth-child(3) {
               transform: rotate(60deg);
               animation-delay: -0.75s;
               background: #93dbe9;
           }.ldio-w61dkjf0pdd div:nth-child(4) {
                transform: rotate(90deg);
                animation-delay: -0.6666666666666666s;
                background: #93dbe9;
            }.ldio-w61dkjf0pdd div:nth-child(5) {
                 transform: rotate(120deg);
                 animation-delay: -0.5833333333333334s;
                 background: #93dbe9;
             }.ldio-w61dkjf0pdd div:nth-child(6) {
                  transform: rotate(150deg);
                  animation-delay: -0.5s;
                  background: #93dbe9;
              }.ldio-w61dkjf0pdd div:nth-child(7) {
                   transform: rotate(180deg);
                   animation-delay: -0.4166666666666667s;
                   background: #93dbe9;
               }.ldio-w61dkjf0pdd div:nth-child(8) {
                    transform: rotate(210deg);
                    animation-delay: -0.3333333333333333s;
                    background: #93dbe9;
                }.ldio-w61dkjf0pdd div:nth-child(9) {
                     transform: rotate(240deg);
                     animation-delay: -0.25s;
                     background: #93dbe9;
                 }.ldio-w61dkjf0pdd div:nth-child(10) {
                      transform: rotate(270deg);
                      animation-delay: -0.16666666666666666s;
                      background: #93dbe9;
                  }.ldio-w61dkjf0pdd div:nth-child(11) {
                       transform: rotate(300deg);
                       animation-delay: -0.08333333333333333s;
                       background: #93dbe9;
                   }.ldio-w61dkjf0pdd div:nth-child(12) {
                        transform: rotate(330deg);
                        animation-delay: 0s;
                        background: #93dbe9;
                    }
        .loadingio-spinner-spinner-kwnzxy788ab {
            width: 100%;
            height: 100%;

            display: inline-block;
            overflow: hidden;
            background: #f1f2f3;
        }
        .ldio-w61dkjf0pdd {
            width: 200px;
            height: 200px;
            position: relative;
            transform: translateZ(0) scale(1);
            backface-visibility: hidden;
            transform-origin: 0 0; /* see note above */
            margin: 0 auto;
            top: 35%;
        }
        .ldio-w61dkjf0pdd div { box-sizing: content-box; }
        /* generated by https://loading.io/ */

        .loading{
            display: none;
            position: fixed;
            top: 0;
            z-index: 9999;
            background-color: rgba(241, 242, 243,0.2);
        }
    </style>
@endsection
@section('script')
    <script src='{{asset('assets/fullcalendar')}}/packages/core/main.js'></script>
    <script src='{{asset('assets/fullcalendar')}}/packages/moment/main.min.js'></script>
    <script src='{{asset('assets/fullcalendar')}}/packages/core/locales/vi.js'></script>
    <script src='{{asset('assets/fullcalendar')}}/packages/interaction/main.js'></script>
    <script src='{{asset('assets/fullcalendar')}}/packages/daygrid/main.js'></script>
    <script src='{{asset('assets/fullcalendar')}}/packages/rrule/main.js'></script>
    <script src='{{asset('assets/fullcalendar')}}/packages/timegrid/main.js'></script>
    <script src='{{asset('assets/fullcalendar')}}/packages/bootstrap/main.js'></script>
    <script src="{{asset('assets/js/calendar.js')}}"></script>
    @if(session()->get('message'))
    <script>
        toastr.success(@json(session()->get('message')));
    </script>
    @endif
    <script !src="">
        @auth
            var user_name = '{{Auth::user()->user_name}}';
        @endauth
        @if(Session::get('room'))
            var room = '{{\Session::get('room')['id']}}';
        @endif
        @if(Session::get('semesters'))
            var semester_id = '{{\Session::get('semesters')['id']}}';
        @endif
        @if(Session::get('semester_now'))
            var now_id = '{{\Session::get('semester_now')['id']}}';
        @endif
        @if(Session::get('success_feedback'))
            toastr.success('Gửi thành công một phản ánh', 'Thành công');
        @endif

        const qualityWeekSemester = @json(\Session::get('semester_now')['number_weeks']);
    </script>
@endsection
@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Thời khóa biểu phòng máy</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item">Thời khóa biểu</div>
                </div>
            </div>

            <div class="section-body">
                <h2 class="section-title">@if(\Session::has('room'))Phòng {{\Session::get('room')['room_id']}}@else Chưa có phòng  @endif</h2>
                <p class="section-lead">
                    @if(session()->has('semesters'))
                        Học kì {{\Session::get('semesters')['semester']}} năm học {{\Session::get('semesters')['school_year']}}
                    @else
                        <span style="color: red">Chưa có học kì mới!</span>
                    @endif
                </p>
                @auth()
                <div class="row">
                    <div class="col-12">
                        <div class="card mb-0">
                            <div class="card-body">
                                <ul class="nav nav-pills">
                                    <li class="nav-item">
                                        <a class="nav-link active" href="#" id="btnExcel">Nhập Excel</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                @endauth
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="card" style="width: 100%; border-radius: 5px; ">
                                    <div class="card-header" style=" background-color: #f5f5f5;min-height: 10px; height: 40px;">
                                        <h4 class="card-title" style="color: #6c757d; "><i class="fas fa-search"></i> Lọc thời khóa biểu</h4>
                                    </div>
                                    <div class="card-body">
                                        <form class="form-inline" action="{{route('calendar.changeSchedules')}}" method="post">
                                            @csrf
                                            <div class="form-group col-4" >
                                                <select name="filterSemester" id="filterSemester">
                                                    @isset($semesters)
                                                        @foreach($semesters as $semester)
                                                            <option value="{{$semester['id']}}" @if($semester['id'] == \Session::get('semesters')['id']) selected @endif>Học kì: {{$semester['semester']}} - năm học: {{$semester['school_year']}}</option>
                                                        @endforeach
                                                    @endisset

                                                </select>
                                            </div>
                                            <div class="form-group col-4">
                                                <select name="filterRoom" id="filterRoom">
                                                    @isset($rooms)
                                                        @foreach($rooms as $room)
                                                            <option value="{{$room['id']}}" @if($room['room_id'] == \Session::get('room')['room_id']) selected @endif>{{$room['room_id']}}</option>
                                                        @endforeach
                                                    @endisset
                                                </select>
                                            </div>
                                            <div class="form-group col-4">
                                                <button type="submit" class="btn btn-primary mb-2" style="margin-right: 5px"><i class="fas fa-search"></i> Lọc dữ liệu</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="fc-overflow">
                                    <div id='calendar'
                                         data-route-load-events="{{route('calendar.getSchedules')}}"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

@endsection
@section('modals')
    @auth
        <div class="modal fade" id="addEventModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
             aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Đăng kí thời khóa biểu</h5>

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div>
                            <h5>Tuần: <span id="weekNow"></span></h5>
                        </div>
                        <form id="formCreatSchedules" method="post" action="{{route('calendar.store')}}">
                            @csrf
                            <div class="row">
                                <div class="card-body col-4">
                                    <div class="form-group">
                                        <label>Học kì:</label>
                                        <input type="text" class="form-control" disabled
                                                @if (Session::get('semester_now'))value="Học kì:{{\Session::get('semester_now')['semester']}} - Năm học:{{\Session::get('semester_now')['school_year']}}"@endif>
                                    </div>

                                    <div class="form-group">
                                        <label>Phòng máy:</label>
                                        <select class="js-example-basic-single form-control select2-input" id="room" name="room"
                                                style="width: 219px">
                                                <option value=""></option>
                                                @isset($rooms)
                                                    @foreach($rooms as $room)
                                                        <option value="{{$room['id']}}" @if($room['room_id'] == \Session::get('room')['room_id']) selected @endif>{{$room['room_id']}}</option>
                                                    @endforeach
                                                @endisset

                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Môn học: <span class="required">(*)</span></label>
                                        <select class="js-example-basic-single form-control select2-input" id="subject" name="subject"
                                                style="width: 219px" >
                                            <option value=""></option>
                                            @foreach($subjects as $subject)
                                                <option value="{{$subject['id']}}">{{$subject['subject_id'] .' - '.$subject['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Tiết bắt đầu:<span class="required">(*)</span></label>
                                        <input type="number" class="form-control" name="lesson_start" id="lesson_start" min="1"
                                               max="13" value="" >
                                    </div>

                                    <div class="form-group">
                                        <label>Tuần bắt đầu:<span class="required">(*)</span></label>
                                        <input type="number" class="form-control week" name="week_start" id="week_start"
                                               value="" >
                                    </div>




                                </div>
                                <div class="card-body col-4">
                                    <div class="form-group">
                                        <label>Giảng viên:</label>
                                        <input type="text" class="form-control" disabled
                                               name="user_name" value="{{Auth::user()->full_name}}">
                                    </div>

                                    <div class="form-group">
                                        <label>Lớp: <span class="required">(*)</span></label>
                                        <input type="text" class="form-control" name="class" id="class" value="" >

                                    </div>

                                    <div class="form-group">
                                        <label>Nhóm môn học: <span class="required">(*)</span></label>
                                        <input type="number" class="form-control" name="group" id="group" value="">

                                    </div>
                                    <div class="form-group">
                                        <label>Số tiết: <span class="required">(*)</span></label>
                                        <input type="number" class="form-control"  name="lesson_quantity" id="lesson_quantity"
                                               value="" >

                                    </div>
                                    <div class="form-group">
                                        <label>Số tuần:<span class="required">(*)</span></label>
                                        <input type="number" class="form-control week" name="week_quantity" id="week_quantity"
                                               value="" >

                                    </div>

                                </div>
                                <div class="card-body col-4">
                                    <div class="form-group">
                                        <label>thời gian đăng kí:</label>
                                        <input type="text" class="form-control" required="" disabled name="time"
                                               value="{{date('d-m-Y H:m')}}">
                                    </div>
                                    <div class="form-group">
                                        <label>Sĩ số:<span class="required">(*)</span></label>
                                        <input type="number" class="form-control" id="quantity" name="quantity"
                                               value="">
                                    </div>
                                    <div class="form-group">
                                        <label>Thứ:</label>
                                        <select class="form-control" id="weekDay" name="weekDay">
                                            @for($i=0;$i<7;$i++)
                                                @if($i==6)
                                                    <option value="{{$i+2}}">Chủ nhật</option>
                                                @else
                                                    <option value="{{$i+2}}">Thứ {{$i+2}}</option>
                                                @endif
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="form-group mb-0">
                                        <label>Ghi chú:</label>
                                        <textarea class="form-control" id="note" name="note"
                                                  style="height: 140px">{{old('note')}}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Chọn tuần học: <span class="required">(*)</span></label>
                                <select class="js-example-basic-single form-control select2-input" id="weekCheck" name="weekCheck[]"
                                        style="width: 219px" multiple="multiple">
                                    <option value=""></option>
                                </select>
                            </div>


                            <div class="modal-footer text-right">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal" >Đóng</button>
                                <button type="submit" class="btn btn-primary" id="btnSubmitCreatSchedules" for="formCreatSchedules">Đăng kí</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editEventModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
             aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Cập nhật yêu cầu</h5>

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="formEditSchedules" method="" action="">
                            @csrf
                            <div class="row">
                                <div class="card-body col-4">
                                    <div class="form-group">
                                        <label>Phòng máy:</label>
                                        <select class="js-example-basic-single form-control select2-input" id="edit_room" name="edit_room"
                                                style="width: 219px">
                                                <option value=""></option>
                                                @isset($rooms)
                                                    @foreach($rooms as $room)
                                                        <option value="{{$room['id']}}" @if($room['room_id'] == \Session::get('room')['room_id']) selected @endif>{{$room['room_id']}}</option>
                                                    @endforeach
                                                @endisset

                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Môn học: <span class="required">(*)</span></label>
                                        <select class="js-example-basic-single form-control select2-input" id="edit_subject" name="edit_subject"
                                                style="width: 219px" >
                                            <option value=""></option>
                                            @foreach($subjects as $subject)
                                                <option value="{{$subject['id']}}">{{$subject['subject_id'] .' - '.$subject['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Tiết bắt đầu:<span class="required">(*)</span></label>
                                        <input type="number" class="form-control" name="edit_lesson_start" id="edit_lesson_start" min="1"
                                               max="13" value="" >
                                    </div>

                                    <div class="form-group">
                                        <label>Tuần bắt đầu:<span class="required">(*)</span></label>
                                        <input type="number" class="form-control weekEdit" name="edit_week_start" id="edit_week_start"
                                               value="" >
                                    </div>

                                </div>
                                <div class="card-body col-4">
                                    <div class="form-group">
                                        <label>Lớp: <span class="required">(*)</span></label>
                                        <input type="text" class="form-control" name="edit_class" id="edit_class" value="" >

                                    </div>

                                    <div class="form-group">
                                        <label>Nhóm môn học: <span class="required">(*)</span></label>
                                        <input type="number" class="form-control" name="edit_group" id="edit_group" value="">

                                    </div>
                                    <div class="form-group">
                                        <label>Số tiết: <span class="required">(*)</span></label>
                                        <input type="number" class="form-control"  name="edit_lesson_quantity" id="edit_lesson_quantity"
                                               value="" >

                                    </div>
                                    <div class="form-group">
                                        <label>Số tuần:<span class="required">(*)</span></label>
                                        <input type="number" class="form-control weekEdit" name="edit_week_quantity" id="edit_week_quantity"
                                               value="" >

                                    </div>

                                </div>
                                <div class="card-body col-4">
                                    <div class="form-group">
                                        <label>Sĩ số:<span class="required">(*)</span></label>
                                        <input type="number" class="form-control" id="edit_quantity" name="edit_quantity"
                                               value="">
                                    </div>
                                    <div class="form-group">
                                        <label>Thứ:</label>
                                        <select class="form-control" id="edit_weekDay" name="edit_weekDay">
                                            @for($i=0;$i<7;$i++)
                                                @if($i==6)
                                                    <option value="{{$i+2}}">Chủ nhật</option>
                                                @else
                                                    <option value="{{$i+2}}">Thứ {{$i+2}}</option>
                                                @endif
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="form-group mb-0">
                                        <label>Ghi chú:</label>
                                        <textarea class="form-control" id="edit_note" name="edit_note"
                                                  style="height: 140px">{{old('note')}}</textarea>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Chọn tuần học: <span class="required">(*)</span></label>
                                        <select class="js-example-basic-single form-control select2-input" id="weekCheckEdit" name="weekCheck[]"
                                                style="width: 219px" multiple="multiple">
                                            <option value=""></option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer text-right">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal" >Đóng</button>
                                <button type="submit" class="btn btn-primary" id="btnSubmitEditEventCalendar" for="formEditSchedules">Lưu</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endauth
    <div class="modal fade" id="detailEventlModal" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chi tiết</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-striped">
                        <tr>
                            <td>
                                <b>Môn học:</b>
                            </td>
                            <td>
                                <span id="detailSubject"></span>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <b>Giảng viên:</b>
                            </td>
                            <td>
                                <span id="detailTeacher"></span>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <b>Lớp:</b>
                            </td>
                            <td>
                                <span id="detailClass"></span>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <b>Số lượng: </b>
                            </td>
                            <td>
                                <span id="detailAmountPpeople"></span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>Nhóm môn học: </b>
                            </td>
                            <td>
                                <span id="detailGroup"></span>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <b>Tiết bắt đầu: </b>
                                <span id="detailStartSession"></span>
                            </td>
                            <td>
                                <b>Thời lượng: </b>
                                <span id="detailNumberSession"></span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>Tuần bắt đầu: </b><span id="detailStartWeek"></span>
                            </td>
                            <td>
                                <b>Tuần kết thúc: </b><span id="detailEndWeek"></span>
                            </td>
                        </tr>
                    </table>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    @auth
                        <button type="button" id="btnEditEvent" class="btn btn-primary">Sửa</button>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="excelModals" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Nhập Excel</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="height: 300px">
                    <form id="formExcel" method="" action="">
                        <div class="form-group">
                            <input id="fileExcel" name="excel" type="file" multiple>
                            <p class="file-text">Thả hoặc chọn tệp của bạn tại đây.</p>
                            <button id="uploadEx" type="submit">Tải lên</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('loading')
    <div class="container-loading">
        <div class="loadingio-spinner-spinner-kwnzxy788ab loading"><div class="ldio-w61dkjf0pdd">
                <div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div>
            </div>
        </div>
    </div>
@endsection
