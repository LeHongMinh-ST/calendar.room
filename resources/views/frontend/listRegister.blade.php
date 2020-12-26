@extends('layouts.master')
@section('title')
    Danh sách thời khóa biểu đã đăng kí
@endsection
@section('css')
    <style>
        .required {
            color: red;
        }

        .select2-selection__rendered {
            padding-top: 6px;
        }
        #actionSelectCheckbox{
            font-size: 14px;
            padding: 10px 15px;
            width: 150px !important;
            margin-bottom: 10px;
        }
    </style>
@endsection
@section('script')
    <script>
        const auth = @json(\Illuminate\Support\Facades\Auth::user());
    </script>
    <script src="{{asset('assets/js/ListSchedule.js')}}"></script>
@endsection
@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Thời khóa biểu đã đăng kí</h1>
        </div>

        <div class="section-body">
            <h2 class="section-title">Danh sách thời khóa biểu đã đăng kí</h2>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card" style="width: 100%; border-radius: 5px; ">
                                <div class="card-header" style=" background-color: #f5f5f5;min-height: 10px; height: 40px;">
                                    <h4 class="card-title" style="color: #6c757d; "><i class="fas fa-search"></i> Lọc thời khóa biểu</h4>
                                </div>
                                <div class="card-body">
                                    <form class="form-inline" action="" method="post" id="formFilterSchedule">
                                        @csrf
                                        <div class="form-group col-3">
                                            <select name="filterSemester" id="filterSemester">
                                                @foreach($semesters as $semester)
                                                    <option value="{{$semester['id']}}" @if($semester['id'] == $now['id']) selected @endif>Học kì: {{$semester['semester']}} - năm học: {{$semester['school_year']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-3">
                                            <select name="filterRoom" id="filterRoom">
                                                <option value="" selected>Tất cả phòng máy</option>
                                                @foreach($rooms as $room)
                                                    <option value="{{$room['id']}}">{{$room['room_id']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-3">
                                            <select name="filterStatus" id="filterStatus">
                                                <option value="0" selected>Chưa xử lý</option>
                                                <option value="1" >Đã xử lý</option>
                                                <option value="2" >Đã hủy</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-3">
                                            <button type="submit" class="btn btn-primary mb-2" style="margin-right: 5px" for="formFilterSchedule"><i class="fas fa-search"></i> Lọc dữ liệu</button>
                                            <button type="button" class="btn btn-danger mb-2" style="margin-right: 5px" id="redoFilter"><i class="fas fa-redo-alt"></i> Đặt lại</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            {{-- <a href="{{route('users.create')}}" class="btn btn-success" title="Thêm mới người dùng"><i class="fas fa-user-plus"></i></a> --}}
                        </div>
                        <div class="card-body">
                            <div class="actionCheckbox">
                                <select name="" class="custom-select custom-select-sm form-control form-control-sm" id="actionSelectCheckbox">
                                    <option value="">Chọn hành động</option>
                                    @if(\Illuminate\Support\Facades\Auth::user()->role_id == 1)
                                        <option id="op-delete" value="delete">Xóa</option>
                                    @endif
                                    <option value="export">Xuất excel</option>
                                </select>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped" id="tableRegisterSchedules">
                                    <thead>
                                    <tr>
                                        <th class="text-center dt-checkboxes-cell dt-checkboxes-select-all"><input type="checkbox" class="dt-checkboxes-all" autocomplete="off"></th>
{{--                                        <th></th>--}}
                                        <th>Phòng máy</th>
                                        <th>Mã môn học</th>
                                        <th>Tên môn học</th>
                                        <th>Họ và tên CBGV</th>
                                        <th>Sĩ số</th>
                                        <th>Thứ</th>
                                        <th>Tiết bắt đầu</th>
                                        <th>Số tiết</th>
                                        <th>Tuần học</th>
                                        <th>Số tuần</th>
                                        <th>Thời gian gửi</th>
                                        <th>Trạng thái</th>
                                        <th>Hành động</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
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
        <div class="modal fade" id="editEventModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
             aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Sửa yêu cầu</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="formEditSchedules">
                            <div class="row">
                                <div class="card-body col-4">

                                    <div class="form-group">
                                        <label>Phòng máy:</label>
                                        <select class="js-example-basic-single form-control" id="edit_room" name="edit_room"
                                                style="width: 219px">
                                                @isset($rooms)
                                                    @foreach($rooms as $room)
                                                        <option value="{{$room['id']}}" @if(\Session::has('room')) @if($room['room_id'] == \Session::get('room')['room_id']) selected @endif @endif>{{$room['room_id']}}</option>
                                                    @endforeach
                                                @endisset

                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Môn học: <span class="required">(*)</span></label>
                                        <select class="js-example-basic-single form-control" id="edit_subject" name="edit_subject"
                                                style="width: 219px">
                                            @foreach($subjects as $subject)
                                                <option value="{{$subject['id']}}">{{$subject['subject_id'] .' - '. $subject['name']}}</option>
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
                                        <input type="number" class="form-control" name="edit_week_start" id="edit_week_start"
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
                                        <input type="number" class="form-control" name="edit_week_quantity" id="edit_week_quantity"
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
                            </div>

                            <div class="modal-footer text-right">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal" >Đóng</button>
                                <button type="button" class="btn btn-primary" id="btnSubmitEditEventCalendar" for="formEditSchedules">Cập nhật</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

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
                    </div>
                </div>
            </div>
        </div>
@endsection
