@extends('layouts.master')
@section('title')
    {{__('Dashboard')}}
@endsection
@section('css')
    <style>
        .btn_action {
            display: flex;
        }

        .btn {
            margin-right: 5px;
        }

        .required {
            color: red;
        }

        .select2 {
            width: 350px !important;
        }

        .select2-selection__rendered {
            padding-top: 6px;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
        }

        input:checked + .slider {
            background-color: #47c363;
        }

        input:focus + .slider {
            box-shadow: 0 0 1px #47c363;
        }

        input:checked + .slider:before {
            -webkit-transform: translateX(26px);
            -ms-transform: translateX(26px);
            transform: translateX(26px);
        }

        /* Rounded sliders */
        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }


    </style>
@endsection
@section('script')t>
    <script src="{{asset('/')}}assets/js/dashboard.js"></script>
@endsection

@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Bảng điều khiển</h1>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-primary">
                            <i class="far fa-user"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Người dùng</h4>
                            </div>
                            <div class="card-body">
                                {{ $user }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-danger">
                            <i class="far fa-newspaper"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Yêu cầu đăng kí</h4>
                            </div>
                            <div class="card-body">
                                {{ $schedule }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-warning">
                            <i class="far fa-file"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Phản ánh</h4>
                            </div>
                            <div class="card-body">
                                {{ $feedback }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-success">
                            <i class="fas fa-circle"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Phòng máy</h4>
                            </div>
                            <div class="card-body">
                                {{ $room }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8 col-md-12 col-12 col-sm-12">
                  <div class="card">
                    <div class="card-header">
                      <h4>Yêu cầu đăng kí phòng máy</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="table_schedules">
                                <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Phòng máy</th>
                                    <th>Tên môn học</th>
                                    <th>Thứ</th>
                                    <th>Tiết bắt đầu</th>
                                    <th>Số tiết</th>
                                    <th>Tuần học</th>
                                    <th>Số tuần</th>
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
                <div class="col-lg-4 col-md-12 col-12 col-sm-12">
                  <div class="card">
                    <div class="card-header">
                      <h4>Danh sách người dùng</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="table_user">
                                <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Họ và tên</th>
                                    <th>Email</th>
                                    <th>Trạng thái</th>
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
