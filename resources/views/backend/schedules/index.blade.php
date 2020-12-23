@extends('layouts.master')
@section('css')
    <style>
        .select2-selection__rendered {
            padding-top: 6px;
        }
    </style>
@endsection
@section('script')
    <script src="{{asset('assets/js/schedule.js')}}"></script>
    <script>
        @if(\Session::has('success'))
            toastr.success('{{\Session::pull('success')}}', 'Thành công');
        @endif
    </script>
@endsection
@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Xác nhận yêu cầu</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{route('backend.dashboard')}}">Bản điều khiển</a>
                </div>
                <div class="breadcrumb-item">Xác nhận yêu cầu</div>
            </div>
        </div>

        <div class="section-body">
            <h2 class="section-title">Danh sách yêu cầu đăng kí thời khóa biểu phòng máy</h2>

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
                            <div class="table-responsive">
                                <table class="table table-striped" id="table_schedules">
                                    <thead>
                                    <tr>
                                        <th>STT</th>
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
