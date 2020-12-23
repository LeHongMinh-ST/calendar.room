@extends('layouts.master')
@section('title')
    {{__('Quản lý khoa')}}
@endsection
@section('css')
    <link rel="stylesheet" href={{ asset('assets/css/style.css') }}>
    <style>
        .p{
            position: absolute;
            top:0;
        }
        .error_unique_room_create p{
            margin-left: 298px;
            color: red;
        }
        .error_unique_room_edit p{
            margin-left: 298px;
            color: red;
        }

        .td{
            position: relative;
        }
        .required{
            color: red;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice, .select2-container--default .select2-results__option[aria-selected=true], .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #6777ef;
            color: #fff;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            margin-right: 5px;
            color: #fff;
        }
        .errors {
            color: red;
            /* background-color: #acf; */
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

        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }
    </style>
@endsection
@section('script')
    <script src="{{asset('assets/js/faculty.js')}}"></script>
@endsection

@section('content')
    <div class="main-content">
        <section class="section">

            <div class="section-header">
                <h1>Khoa</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{route('backend.dashboard')}}">Trang chủ</a></div>
                    <div class="breadcrumb-item"><a href="{{route('backend.faculty.index')}}">QLTT chung</a></div>
                    <div class="breadcrumb-item">Danh sách khoa</div>
                </div>
            </div>
            <div class="section-body">
                <h2 class="section-title">Danh sách khoa</h2>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <a href="" class="btn btn-success btn-add" style="border-radius: 5px!important;">Thêm mới</a>
                            </div>
                            <div class="card-body">
                                <table class="table table-striped" id="listFaculty">
                                    <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th >Mã khoa</th>
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
        </section>
    </div>

    <div class="modal fade" id="addModalFaculty" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="  modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="formAddFaculty">
                    <div class="card-header">
                        <h4>Thêm mới</h4>
                    </div>
                    <div class="card-body ">
                        <div class="form-group">
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Mã khoa<span class="required">(*)</span></label>
                                <div class="col-sm-12 col-md-7">
                                    <input type="text" class="form-control" name="add_faculty_id" id="add_faculty_id" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Tên khoa<span class="required">(*)</span></label>
                                <div class="col-sm-12 col-md-7">
                                    <input type="text" class="form-control" name="add_faculty_name" id="add_faculty_name"/>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row mb-4">
                            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                            <div class="col-sm-12 col-md-7">
                                <button type="" class="btn btn-primary btn-save" id="btnSaveformAddFaculty" for="formAddFaculty" >Thêm</button>
                                <button  class="btn btn-secondary" data-dismiss="modal">hủy</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editModalFaculty" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class=" modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="formEditFaculty">
                    <div class="card-header">
                        <h4>Sửa thông tin</h4>
                    </div>
                    <div class="card-body ">
                        <div class="form-group">
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Mã khoa<span class="required">(*)</span></label>
                                <div class="col-sm-12 col-md-7">
                                    <input type="text" class="form-control" name="edit_faculty_id" id="edit_faculty_id" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Tên khoa<span class="required">(*)</span></label>
                                <div class="col-sm-12 col-md-7">
                                    <input type="text" class="form-control" name="edit_faculty_name" id="edit_faculty_name"/>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row mb-4">
                            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                            <div class="col-sm-12 col-md-7">
                                <button  class="btn btn-primary btn-save" id="btnSaveformEditFaculty" for="formEditFaculty" >Sửa</button>
                                <button  class="btn btn-secondary" data-dismiss="modal">hủy</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


@endsection
