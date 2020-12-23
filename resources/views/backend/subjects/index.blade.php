@extends('layouts.master')
@section('title','Quản lí môn học')
@section('script')
    <script src="{{ asset('assets/js/subject.js') }}"></script>
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
            width: 100% !important;
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
@section('content')
    <div class="main-content">
        <div class="section">
            <div class="section-header">
                <h1>Môn học</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Trang chủ</a></div>
                    <div class="breadcrumb-item">Danh sách môn học</div>
                </div>
            </div>
            <div class="section-body">
                <h2 class="section-title">Danh sách môn học</h2>


                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <a id="add_subject" class="btn btn-success" style="border-radius: 5px!important;">Thêm mới</a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="listSubject">
                                        <thead>
                                        <tr>
                                            <th>
                                                Mã môn học
                                            </th>
                                            <th>Tên môn học</th>
                                            <th>Bộ môn</th>
                                            <th>Người tạo</th>
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
    </div>
{{--modal add--}}
    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="" id="add_form" method="">

                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Thêm mới môn học</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="task-name" class="col-sm-3 control-label">Mã môn học</label>
                            <div class="col-sm">
                                <input type="text" name="subject_id" id="add_subject_id" class="form-control"
                                       value="">
                                <p style="color: red;" class="error_at_form_edit errorName"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="task-name" class="col-sm-3 control-label">Tên môn học</label>
                            <div class="col-sm">
                                <input type="text" name="name" id="add_name" class="form-control"
                                       value="">
                                <p style="color: red;" class="error_at_form_edit errorName"></p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="task-status" class="col-sm-3 control-label">Bộ Môn</label>

                            <div class="col-sm">
                                <select name="department_id" id="add_department_id" class="form-control select2-input">
                                    <option value=""></option>
                                    @foreach($departments as $department)
                                        <option value="{{$department['id']}}">{{$department['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">hủy</button>
                        <button id="add-save" class="btn btn-primary add-save">Thêm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
{{--    modal edit--}}
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="" id="edit_form">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">chỉnh sửa môn học</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="task-name" class="col-sm-3 control-label">Tên môn học</label>
                            <div class="col-sm">
                                <input type="text" name="name" id="edit_name" class="form-control"
                                       value="">
                                <p style="color: red;" class="error_at_form_edit errorName"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="task-name" class="col-sm-3 control-label">Mã môn học</label>
                            <div class="col-sm">
                                <input type="text" name="subject_id" id="edit_subject_id" class="form-control"
                                       value="">
                                <p style="color: red;" class="error_at_form_edit errorName"></p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="task-status" class="col-sm-3 control-label">Bộ Môn</label>

                            <div class="col-sm">
                                <select name="department_id" id="edit_department_id" class="form-control select2-input" required="required">
                                    @foreach($departments as $department)
                                        <option value="{{$department['id']}}">{{$department['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">hủy</button>
                        <button class="btn btn-primary edit-save" value="">lưu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
{{--        modal show--}}
    <div class="modal fade" id="showModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">xem thông tin môn học</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div style="height: 300px;" class="col-sm-12">

                    <table>
                        <tr>
                            <td>Tên môn học: </td>
                            <td id="show_name"></td>
                        </tr>
                        <tr>
                            <td>Mã môn học: </td>
                            <td id="show_subject_id"></td>
                        </tr>
                        <tr>
                            <td>Bộ môn: </td>
                            <td id="show_department">
                            </td>
                        </tr>
                        <tr>
                            <td>Người tạo: </td>
                            <td id="show_user_created"></td>
                        </tr>
                        <tr>
                            <td>Ngày tạo: </td>
                            <td id="show_created_at"></td>
                        </tr>
                        <tr>
                            <td>Chỉnh sửa lần cuối: </td>
                            <td id="show_updated_at">
                            </td>
                        </tr>

                    </table>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">đóng</button>
                </div>
            </div>
        </div>
    </div>

    @endsection
