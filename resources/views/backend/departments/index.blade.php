@extends('layouts.master')
@section('title','Quản lí bộ môn')
@section('script')
    <script src="{{ asset('assets/js/department.js') }}"></script>
    <script !src="">
        @if(\Session::has('success'))
        toastr.success('{{\Session::pull('success')}}', 'Thành công');
        @endif

    </script>

@endsection
@section('css')
    <style>
        .btn_action {
            display: flex;
        }

        .btn {
            margin-right: 5px;
            color: white !important;
        }

        .required {
            color: red;
        }

        /*.select2 {*/
        /*    width: 350px !important;*/
        /*}*/

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
@section('content')
    <div class="main-content">
        <div class="section">
            <div class="section-header">
                <h1>Bộ môn</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Trang chủ</a></div>
                    <div class="breadcrumb-item"><a href="#">Quản lí bộ môn</a></div>
                    <div class="breadcrumb-item">Danh sách bộ môn</div>
                </div>
            </div>
            <div class="section-body">
                <h2 class="section-title">Danh sách bộ môn</h2>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <a id="btn-add" class="btn btn-success" style="border-radius: 5px!important;">Thêm mới</a>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="card" style="width: 100%; border-radius: 5px; ">
                                        <div class="card-header" style=" background-color: #f5f5f5;min-height: 10px; height: 40px;">
                                            <h4 class="card-title" style="color: #6c757d; "><i class="fas fa-search"></i> Lọc bộ môn</h4>
                                        </div>
                                        <div class="card-body">
                                            <form class="form-inline" action="" method="post" id="formFilterFaclty">
                                                @csrf
                                                <div class="form-group col-4" >
                                                    <select name="filterFaculty" id="filterFaculty" class="custom-select select2-selection__rendered">
                                                        <option value="">Tất cả các khoa</option>
                                                        @forelse($fillterFaculty as $faculty)
                                                            <option value="{{$faculty['id']}}">{{$faculty['name']}}</option>
                                                        @empty
                                                            <p>Không có dữ liệu khoa</p>
                                                        @endforelse
                                                    </select>
                                                </div>
                                                <div class="form-group col-4">
                                                    <button class="btn btn-primary mb-2 filter" id="filter" style="margin-right: 5px"><i class="fas fa-search "></i> Lọc dữ liệu</button>
                                                    <button class="btn btn-danger mb-2 reset" id="resetFilter"><i class="fas fa-undo-alt "></i> Đặt lại</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div></div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                    <table class="table table-striped" id="listDepartment">
                                        <thead>
                                        <tr>
                                            <th>STT</th>
                                            <th>Mã bộ môn</th>
                                            <th>Tên bộ môn</th>
                                            <th>Tên khoa</th>
                                            <th>Trạng thái</th>
                                            <th>Hành động</th>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="AddModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
             aria-hidden="true">
            <div class="modal-lg modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Thêm mới bộ môn</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div  class="col-sm-12">
                                <form id="frmAdd">
                                    <div class="form-group row mb-4">
                                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Khoa</label>
                                        <div class="col-sm-12 col-md-7">
                                            <select class="form-control select2 select2-input" id="faculty_id" name="faculty_id" >
                                                <option value=""></option>
                                                @forelse($faculties as $faculty)
                                                    <option value="{{$faculty['id']}}">{{$faculty['name']}}</option>
                                                @empty
                                                    <p>Chưa có phòng máy</p>
                                                @endforelse
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row mb-4">
                                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Mã bộ môn</label>
                                        <div class="col-sm-12 col-md-7">
                                            <input type="text" class="form-control" id="department_id" name="department_id" >
                                        </div>
                                    </div>
                                    <div class="form-group row mb-4">
                                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Tên bộ môn</label>
                                        <div class="col-sm-12 col-md-7">
                                            <input type="text" class="form-control" name="name" id="name" >
                                        </div>
                                    </div>
                                    <div class="form-group row mb-4">
                                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                                        <div class="col-sm-12 col-md-7">
                                            <button class="btn btn-primary btn-save" id="btnSaveFormAdd" >Thêm mới</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="EditModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
             aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Chỉnh sửa bộ môn</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div  class="col-sm-12">
                                <form id="frmEdit">
                                    <div class="form-group row mb-4">
                                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Khoa</label>
                                        <div class="col-sm-12 col-md-7">
{{--                                            <input type="text" class="form-control" id="faculty_id_edit" name="faculty_id_edit" value="" disabled/>--}}
                                            <select class="form-control select2 select2-input" id="faculty_id_edit" name="faculty_id_edit" >
                                                @forelse($fillterFaculty as $faculty)
                                                    <option value="{{$faculty['id']}}">{{$faculty['name']}}</option>
                                                @empty
                                                    <p>Chưa có phòng máy</p>
                                                @endforelse
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row mb-4">
                                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Mã bộ môn</label>
                                        <div class="col-sm-12 col-md-7">
                                            <input type="text" class="form-control" id="department_id_edit" name="department_id_edit" value="{{old('department_id')}}">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-4">
                                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Tên bộ môn</label>
                                        <div class="col-sm-12 col-md-7">
                                            <input type="text" class="form-control" name="name_edit" id="name_edit"  value="{{old('name')}}">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-4">
                                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                                        <div class="col-sm-12 col-md-7">
                                            <button type="" class="btn btn-primary btn-save" val="" id="btnSaveformEdit" for="frmEdit" >Lưu</button>
                                            <button  class="btn btn-secondary" data-dismiss="modal">hủy</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
