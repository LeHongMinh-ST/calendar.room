@extends('layouts.master')
@section('title')
    {{__('Quản lý phòng máy')}}
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
    <script src="{{asset('assets/js/room.js')}}"></script>
@endsection

@section('content')
    <div class="main-content">
        <section class="section">

            <div class="section-header">
                <h1>Phòng máy</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{route('backend.dashboard')}}">Trang chủ</a></div>
                    <div class="breadcrumb-item"><a href="{{route('backend.room.index')}}">QLTT chung</a></div>
                    <div class="breadcrumb-item">Danh sách phòng máy</div>
                </div>
            </div>
            <div class="section-body">
                <h2 class="section-title">Danh sách phòng máy</h2>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <a href="" class="btn btn-success btn-add" style="border-radius: 5px!important;">Thêm mới</a>
                            </div>
                            <div class="card-body">
                                <table class="table table-striped" id="listRoom">
                                    <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th >Mã phòng</th>
                                        <th>Tên phòng</th>
                                        <th>Số máy</th>
                                        <th>Địa chỉ</th>
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

    <div class="modal  fade" id="showModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header" style="padding: 25px">
                    <h5 class="modal-title" id="exampleModalLongTitle" style="padding-left: 13px;">Thông tin phòng máy</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div  class="col-sm-12" style="padding-left: 35px;padding-right: 38px;">
                    <table class="table table-striped" id="listRoom">
                        <tr>
                            <td style="width: 30%">Mã phòng máy: </td>
                            <td id="room_id"></td>
                        </tr>
                        <tr>
                            <td>Tên phòng máy: </td>
                            <td id="name"></td>
                        </tr>
                        <tr>
                            <td>Số lượng máy: </td>
                            <td id="computer_number"></td>
                        </tr>
                        <tr>
                            <td>Địa chỉ: </td>
                            <td id="address"></td>
                        </tr>
                        <tr>
                            <td>Môn học: </td>
                            <td id="subject"></td>
                        </tr>
                        <tr>
                            <td>Phần mềm: </td>
                            <td id="software"></td>
                        </tr>

                    </table>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">đóng</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class=" modal-xl  modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="formAddRoom">
                    <div class="card-header">
                        <h4>Thêm mới</h4>
                    </div>
                    <div class="card-body ">
                        <div class="form-group">
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Mã phòng máy<span class="required">(*)</span></label>
                                <div class="col-sm-12 col-md-7">
                                    <input type="text" class="form-control" name="add_room_id" id="add_room_id" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Tên phòng máy<span class="required">(*)</span></label>
                                <div class="col-sm-12 col-md-7">
                                    <input type="text" class="form-control" name="add_name" id="add_name"/>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-group row mb-4" id="">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Số lượng máy<span class="required">(*)</span></label>
                                <div class="col-sm-12 col-md-7">
                                    <input type="text" class="form-control " name="add_computer_number" id="add_computer_number"/>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-group row mb-4" id="">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Địa chỉ<span class="required">(*)</span></label>
                                <div class="col-sm-12 col-md-7">
                                    <input type="text" class="form-control " name="add_address" id="add_address" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-group row mb-4 " id="">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Môn học<span class="required">(*)</span></label>
                                <div class="col-sm-12 col-md-7" id="">
                                    <select class="form-control select2 select2-input" multiple="" name="add_subjects" id="add_subjects">
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-group row mb-4" id="">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Phần mềm</label>
                                <div class="col-sm-12 col-md-7">
                                    <input type="text" class="form-control " name="add_software" id="add_software"/>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row mb-4">
                            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                            <div class="col-sm-12 col-md-7">
                                <button type="" class="btn btn-primary btn-save" id="btnSaveformAddRoom" for="formAddRoom" >Thêm</button>
                                <button  class="btn btn-secondary" data-dismiss="modal">hủy</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class=" modal-xl  modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="formEditRoom">
                    <div class="card-header">
                        <h4>Sửa thông tin</h4>
                    </div>
                    <div class="card-body ">
                        <div class="form-group">
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Mã phòng máy<span class="required">(*)</span></label>
                                <div class="col-sm-12 col-md-7">
                                    <input type="text" class="form-control" name="edit_room_id" id="edit_room_id" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Tên phòng máy<span class="required">(*)</span></label>
                                <div class="col-sm-12 col-md-7">
                                    <input type="text" class="form-control" name="edit_name" id="edit_name"/>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-group row mb-4" id="">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Số lượng máy<span class="required">(*)</span></label>
                                <div class="col-sm-12 col-md-7">
                                    <input type="text" class="form-control " name="edit_computer_number" id="edit_computer_number"/>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-group row mb-4" id="">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Địa chỉ<span class="required">(*)</span></label>
                                <div class="col-sm-12 col-md-7">
                                    <input type="text" class="form-control " name="edit_address" id="edit_address" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-group row mb-4 " id="">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Môn học<span class="required">(*)</span></label>
                                <div class="col-sm-12 col-md-7" id="">
                                    <select class="form-control select2" multiple="" name="edit_subjects" id="edit_subjects">
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-group row mb-4" id="">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Phần mềm</label>
                                <div class="col-sm-12 col-md-7">
                                    <input type="text" class="form-control " name="edit_software" id="edit_software"/>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row mb-4">
                            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                            <div class="col-sm-12 col-md-7">
                                <button type="" class="btn btn-primary btn-save" id="btnSaveformEditRoom" for="formEditRoom" >Sửa</button>
                                <button  class="btn btn-secondary" data-dismiss="modal">hủy</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


@endsection
