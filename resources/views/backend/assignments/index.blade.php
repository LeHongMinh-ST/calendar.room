@extends('layouts.master')
@section('title')
    {{__('Phân công lịch trực')}}
@endsection
@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <link rel="stylesheet" href={{ asset('assets/css/style.css') }}>
    <style>
        .td{
            position: relative;
        }
        .p{
            position: absolute;
            top:0;
        }
        .form_time_assingment p{
            margin-left: 213px;
        }
        .select2-selection__rendered {
            padding-top: 6px;
        }
        .messageCannotCreate{
            background-color: #f2dede;
            color: #a94442;
            width: 100%
        }
    </style>
@endsection
@section('script')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="{{asset('assets/js/assignment.js')}}"></script>
@endsection

@section('content')
    <div class="main-content">
        <section class="section">

            <div class="section-header">
                <h1>Lịch phân công trực</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{route('backend.dashboard')}}">Trang chủ</a></div>
                    <div class="breadcrumb-item"><a href="{{route('backend.room.index')}}">QLTT chung</a></div>
                    <div class="breadcrumb-item">Danh sách lịch trực</div>
                </div>
            </div>
            <div class="section-body">
                <h2 class="section-title">Danh sách lịch trực</h2>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                @if(!empty($semestersNow))
                                    <button href="" class="btn btn-success btn-add" id="btnAddAssignment" value="{{$idSemesterNow}}" title="Thêm mới lịch trực">Thêm mới</button>
                                @else
                                    <div class="alert messageCannotCreate" >
                                        <strong>Ngoài thời gian phân công!</strong>
                                    </div>
                                @endif
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="card" style="width: 100%; border-radius: 5px; ">
                                        <div class="card-header" style=" background-color: #f5f5f5;min-height: 10px; height: 40px;">
                                            <h4 class="card-title" style="color: #6c757d; "><i class="fas fa-search"></i> Lọc lịch trực</h4>
                                        </div>
                                        <div class="card-body">
                                            <form class="form-inline" action="" method="post" id="formFilterFeedback">
                                                @csrf
                                                <div class="form-group col-4" >
                                                    <select name="filterStatus" id="filterSemester" class="custom-select select2-selection__rendered">
                                                        @if(!empty($semestersNow))
                                                            <option value="">Học kỳ hiện tại</option>
                                                            @forelse($semesters as $semester)
                                                                <option value="{{$semester->id}}">{{'Học kỳ: '.$semester->semester.' - Năm học: '.$semester->school_year}}</option>
                                                            @endforeach
                                                        @else
                                                            <option value="">Tất cả học kỳ</option>
                                                            @forelse($semesters as $semester)
                                                                <option value="{{$semester->id}}">{{'Học kỳ: '.$semester->semester.' - Năm học: '.$semester->school_year}}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                                <div class="form-group col-4">
                                                    <select name="filterRoom" id="filterRoom" class="select2-selection__rendered">
                                                        <option value="">Tất cả các phòng</option>
                                                        @forelse($rooms as $room)
                                                            <option value="{{$room->id}}">{{$room->name}}</option>
                                                        @empty
                                                            <option value="Không có dữ liệu phòng máy " disabled></option>
                                                        @endforelse
                                                    </select>
                                                </div>
                                                <div class="form-group col-4">
                                                    <button class="btn btn-primary mb-2 filter" id="filter"  style="margin-right: 5px"><i class="fas fa-search "></i> Lọc dữ liệu</button>
                                                    <button class="btn btn-danger mb-2 reset" id="resetFilter"><i class="fas fa-undo-alt "></i> Đặt lại</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div></div>

                            <div class="card-body">
                                <table class="table table-striped" id="listAssignment">
                                    <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th >Mã phòng</th>
                                        <th>Học kỳ</th>
                                        <th>Họ tên cán bộ trực</th>
                                        <th>Thời gian bắt đầu</th>
                                        <th>Thời gian kết thúc</th>
                                        <th>Hành động</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    //
    <div class="modal  fade" id="showModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header" style="padding: 25px">
                    <h5 class="modal-title" id="exampleModalLongTitle" style="padding-left: 13px;">Thông tin lịch trực</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div  class="col-sm-12" style="padding-left: 35px;padding-right: 38px;">

                    <table class="table table-striped">
                        <tr>
                            <td style="width: 30%">Phòng máy: </td>
                            <td id="show_room"></td>
                        </tr>
                        <tr>
                            <td>Học kỳ: </td>
                            <td id="show_semester">
                            </td>
                        </tr>
                        <tr>
                            <td>Họ và tên cán bộ trực: </td>
                            <td id="show_technicians_name"></td>
                        </tr>
                        <tr>
                            <td>Số điện thoại: </td>
                            <td id="show_phone"></td>
                        </tr>
                        <tr>
                            <td>Thời gian trực: </td>
                            <td id="show_time_assingment"></td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">đóng</button>
                </div>
            </div>
        </div>
    </div>

    //Modal add
    <div class="modal fade" id="addAssignmentModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class=" modal-lg modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="formAddAssignment">
                    <div class="card-header">
                        <h4>Thêm mới lịch trực</h4>
                    </div>
                    <div class="card-body ">
                        <div class="form-group">
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Học kỳ
                                    <span class="required"></span></label>
                                <div class="col-sm-12 col-md-7">
                                    @if(!empty($semestersNow))
                                        <input type="text" class="form-control" id="semester_create" name="semester_create" value="{{$semestersNow}}" disabled/>
                                    @else
                                        <input type="text" class="form-control"  value="Chưa có học kỳ hiện tại" disabled/>
                                    @endif
                                </div>
                            </div>
                            <div class="valid-feedback">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Phòng
                                    máy<span class="required">(*)</span></label>
                                <div class="col-sm-12 col-md-7">
                                    <select class="form-control select2 select2-input" id="room_id_create" name="room_id_create" >
                                        <option value=""></option>
                                        @forelse($rooms as $room)
                                            <option value="{{$room->id}}">{{$room->name}}</option>
                                        @empty
                                            <p>Chưa có phòng máy</p>
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                            <div class="valid-feedback">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Họ và
                                    tên cán bộ trực<span class="required">(*)</span></label>
                                <div class="col-sm-12 col-md-7">
                                    <input type="text" class="form-control" id="name_user_create" name="name_user_create" value=""/>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Số điện
                                    thoại<span class="required">(*)</span></label>
                                <div class="col-sm-12 col-md-7">
                                    <input type="text" class="form-control " id="phone_create" name="phone_create" value=""/>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="form-group row mb-4" id="datepicker">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Thời
                                    gian trực<span class="required">(*)</span></label>
                                <div class="col-sm-12 col-md-7" id="datepicker" >
                                    <input type="text" class="form-control " id="time_assignment_create" name="time_assignment_create" value="" placeholder="dd/mm/yyyy - dd/mm/yyyy"/>
                                </div>
                                <div class="form_time_assingment">
                                    <p></p>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-4">
                            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                            <div class="col-sm-12 col-md-7">
                                <button type="" class="btn btn-primary " id="btnSaveformAddAssignment" data-id="{{$idSemesterNow}}" for="formAddAssignment" >Thêm</button>
                                <button  class="btn btn-secondary" data-dismiss="modal">hủy</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class=" modal-lg modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="formEditAssignment">
                    <div class="card-header">
                        <h4>Sửa thông tin</h4>
                    </div>
                    <div class="card-body ">
                        <div class="form-group">
                            <div class="form-group row mb-4 ">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Học kỳ<span class="required">(*)</span></label>
                                <div class="col-sm-12 col-md-7">
                                    <input type="text" class="form-control" id="semester_edit" name="semester_edit" value="" disabled/>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Phòng
                                    máy<span class="required">(*)</span></label>
                                <div class="col-sm-12 col-md-7">
                                    <select class="form-control selectric select2-input" id="room_id_edit" name="room_id_edit" >
                                        @forelse($rooms as $room)
                                            <option value="{{$room->id}}">{{$room->name}}</option>
                                        @empty
                                            <p>Không có dữ liệu phòng máy</p>
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                            <div class="valid-feedback">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Họ và
                                    tên cán bộ trực<span class="required">(*)</span></label>
                                <div class="col-sm-12 col-md-7">
                                    <input type="text" class="form-control" id="name_user_edit" name="name_user_edit" value=""/>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Số điện
                                    thoại<span class="required">(*)</span></label>
                                <div class="col-sm-12 col-md-7">
                                    <input type="text" class="form-control " id="phone_edit" name="phone_edit" value=""/>

                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-group row mb-4" id="datepicker">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Thời
                                    gian trực<span class="required">(*)</span></label>
                                <div class="col-sm-12 col-md-7" id="datepicker" >
                                    <input type="text" class="form-control " id="time_assignment_edit" name="time_assignment_edit" value="" placeholder="dd/mm/yyyy - dd/mm/yyyy"/>
                                </div>
                                <div class="form_time_assingment">
                                    <p></p>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-4">
                            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                            <div class="col-sm-12 col-md-7">
                                <button type="" class="btn btn-primary btn-save" val="" id="btnSaveformEditAssignment" for="formEditAssignment" >Lưu</button>
                                <button  class="btn btn-secondary" data-dismiss="modal">hủy</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


@endsection
