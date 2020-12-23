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
        .required {
            color: red;
        }

        .select2-selection__rendered {
            padding-top: 6px;
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
                <h2 class="section-title">Phòng {{\Session::get('room')['room_id']}}</h2>
                <p class="section-lead">
                    @if(session()->has('semesters'))
                        Học kì {{\Session::get('semesters')['semester']}} năm học {{\Session::get('semesters')['school_year']}}
                    @else
                        <span style="color: red">Chưa có học kì mới!</span>
                    @endif
                </p>
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
@endsection
