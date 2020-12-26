@extends('layouts.master')
@section('title')
    Đăng kí thời khóa biểu
@endsection
@section('css')
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
@if(session()->get('message'))
    <script>
        toastr.success(@json(session()->get('message')));
    </script>
@endif
<script>
    const qualityWeekSemester = @json(\Session::get('semester_now')['number_weeks']);
</script>
<script src="{{asset('assets/js/registerCalendar.js')}}"></script>
@endsection
@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Thời khóa biểu</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{route('backend.dashboard')}}">Bảng điều khiển</a>
                    </div>
                    <div class="breadcrumb-item">Đăng kí thời khóa biểu</div>
                </div>
            </div>

            <div class="section-body">
                <h2 class="section-title">Đăng kí</h2>
                <p class="section-lead">
                    {!! isset($weekNow) ? 'Tuần hiện tại: Tuần '.$weekNow : '<span style="color: red">Ngoài thời gian đăng kí !<span>'!!}
                </p>

                <div class="row mt-4">
                    <div class="col-12 ">
                        <div class="card">
                            <form method="post" action="{{route('calendar.store')}}" id="formAddEventCalendar">
                                @csrf
                                <div class="row">
                                    <div class="card-body col-4">
                                        <div class="form-group">
                                            <label>Học kì:</label>
                                            <input type="text" class="form-control" disabled
                                                   value="Học kì:{{\Session::get('semester_now')['semester']}} - Năm học:{{\Session::get('semester_now')['school_year']}}">
                                        </div>
                                        <div class="form-group">
                                            <label>Phòng máy:</label>
                                            <select class="js-example-basic-single form-control  select2-input" id="room" name="room">
                                                <option value=""></option>
                                                @foreach($rooms as $room)
                                                    <option value="{{$room['id']}}">{{$room['room_id']}}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>Môn học: <span class="required">(*)</span></label>
                                            <select class="js-example-basic-single form-control  select2-input" id="subject" name="subject">
                                                <option value=""></option>
                                                @foreach($subjects as $subject)
                                                    <option value="{{$subject['id']}}">{{$subject['subject_id'] .' - '.$subject['name']}}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>Tiết bắt đầu:<span class="required">(*)</span></label>
                                            <input type="number" class="form-control" id="lesson_start" name="lesson_start" min="1"
                                                   max="13" value="{{old('lesson_start')}}">
                                        </div>

                                        <div class="form-group">
                                            <label>Tuần bắt đầu:<span class="required">(*)</span></label>
                                            <input type="number" class="form-control week" id="week_start" name="week_start"
                                                   value="{{old('week_start')}}">
                                        </div>

                                    </div>
                                    <div class="card-body col-4">
                                        <div class="form-group">
                                            <label>Giảng viên:</label>
                                            <input type="text" class="form-control" disabled id="user_name"
                                                   name="user_name" value="{{Auth::user()->full_name}}">
                                        </div>

                                        <div class="form-group">
                                            <label>Lớp: <span class="required">(*)</span></label>
                                            <input type="text" class="form-control" id="class" name="class" value="{{old('class')}}">
                                        </div>

                                        <div class="form-group">
                                            <label>Nhóm môn học: <span class="required">(*)</span></label>
                                            <input type="text" class="form-control" id="group" name="group" value="{{old('group')}}">
                                        </div>
                                        <div class="form-group">
                                            <label>Số tiết: <span class="required">(*)</span></label>
                                            <input type="text" class="form-control" id="lesson_quantity" name="lesson_quantity" value="{{old('lesson_quantity')}}">
                                        </div>
                                        <div class="form-group">
                                            <label>Số tuần:<span class="required">(*)</span></label>
                                            <input type="number" class="form-control week" id="week_quantity" name="week_quantity" required=""
                                                   value="{{old('week_quantity')}}">
                                            <div class="invalid-feedback">
                                                Bạn chưa nhập số tuần
                                            </div>
                                        </div>

                                    </div>
                                    <div class="card-body col-4">
                                        <div class="form-group">
                                            <label>thời gian đăng kí:</label>
                                            <input type="text" class="form-control" disabled id="time" name="time"
                                                   value="{{date('d-m-Y H:m')}}">
                                        </div>
                                        <div class="form-group">
                                            <label>Sĩ số:<span class="required">(*)</span></label>
                                            <input type="text" class="form-control" id="quantity" name="quantity"
                                                   value="{{old('quantity')}}" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Thứ:</label>
                                            <select class="form-control" name="weekDay" id="weekDay">
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
                                            <textarea class="form-control" name="note" id="note"
                                                      style="height: 140px">{{old('note')}}</textarea>
                                        </div>

                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label>Chọn tuần học: <span class="required">(*)</span></label>
                                            <select class="js-example-basic-single form-control select2-input" id="weekCheck" name="weekCheck[]"
                                                    style="width: 219px" multiple="multiple">
                                                <option value=""></option>
                                            </select>
                                        </div>
                                    </div>

                                </div>
                                @if($weekNow!=null && Session::get('semester_now') !=null)

                                    @if($weekNow <= \Session::get('semester_now')['number_weeks'])
                                    <div class="card-footer text-right">
                                        <button class="btn btn-primary" id="btnSubmitAddEventCalendar" for="formAddEventCalendar">Đăng kí</button>
                                    </div>
                                    @endif

                                @endif
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>


@endsection



