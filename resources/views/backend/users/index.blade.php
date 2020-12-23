@extends('layouts.master')
@section('title')
    Danh sách người dùng
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

@section('script')
    <script src="{{ asset('assets/js/user.js') }}"></script>
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
                <h1>Người dùng</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{route('backend.dashboard')}}">Bản điều khiển</a>
                    </div>
                    <div class="breadcrumb-item">Người dùng</div>
                </div>
            </div>

            <div class="section-body">
                <h2 class="section-title">Danh sách người dùng</h2>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <a href="" class="btn btn-success" id="btnAddUser" title="Thêm mới người dùng">Thêm mới</a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="table_user">
                                        <thead>
                                        <tr>
                                            <th>STT</th>
                                            <th>Tên tài khoản</th>
                                            <th>Họ và tên</th>
                                            <th>Email</th>
                                            <th>Nhóm người dùng</th>
                                            <th>Số điện thoại</th>
                                            <th>Bộ môn</th>
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
<div class="modal fade" id="addUserModal" tabindex="-1" role="dialog"aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm mới tài khoản</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 ">
                        <div class="card">
                            <form class="" method="post" action="" id="formAddUser">
                                <div class="row">
                                    <div class="card-body col-6">
                                        <div class="form-group">
                                            <label>Tên đăng nhập: <span class="required">(*)</span></label>
                                            <input type="text" class="form-control" id="user_name" name="user_name" value="">
                                        </div>

                                        <div class="form-group">
                                            <label>Họ và tên: <span class="required">(*)</span></label>
                                            <input type="text" class="form-control" id="full_name" name="full_name" value="">
                                        </div>

                                        <div class="form-group">
                                            <label>Bộ môn:</label>
                                            <select class="js-example-basic-single form-control select2-input" id="department" name="department">
                                                <option value=""></option>
                                                @foreach($departments as $department)
                                                    <option value="{{$department['id']}}">{{$department['name']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Nhóm người dùng:</label>
                                            <select class="js-example-basic-single form-control select2-input" id="role" name="role">
                                                <option value=""></option>
                                                <option value="1">Quản lý</option>
                                                <option value="2">Kĩ thuật viên</option>
                                                <option value="0">Giảng viên</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="card-body col-6">
                                        <div class="form-group">
                                            <label>Email:<span class="required">(*)</span></label>
                                            <input type="email" class="form-control" id="email" name="email" value="">

                                        </div>
                                        <div class="form-group">
                                            <label>Phone:</label>
                                            <input type="text" class="form-control" id="phone" name="phone" value="">

                                        </div>
                                        <div class="form-group mb-0">
                                            <label>Ghi chú:</label>
                                            <textarea class="form-control" name="note" style="height: 150px"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-right">
                                    <button class="btn btn-primary" for="formAddUser">Thêm</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog"aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sửa thông tin tài khoản</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 ">
                        <div class="card">
                            <form class="" method="post" action="" id="formEditUser">
                                <div class="row">
                                    <div class="card-body col-6">
                                        <div class="form-group">
                                            <label>Tên đăng nhập: <span class="required">(*)</span></label>
                                            <input type="text" class="form-control" id="edit_user_name" name="edit_user_name" value="">
                                        </div>

                                        <div class="form-group">
                                            <label>Họ và tên: <span class="required">(*)</span></label>
                                            <input type="text" class="form-control" id="edit_full_name" name="edit_full_name" value="">
                                        </div>

                                        <div class="form-group">
                                            <label>Bộ môn: <span class="required">(*)</span></label>
                                            <select class="js-example-basic-single form-control select2-input" id="edit_department" name="edit_department">
                                                @foreach($departments as $department)
                                                    <option value="{{$department['id']}}">{{$department['name']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Nhóm người dùng: <span class="required">(*)</span></label>
                                            <select class="js-example-basic-single form-control select2-input" id="edit_role" name="edit_role">
                                                    <option value="1">Quản lý</option>
                                                    <option value="2">Kĩ thuật viên</option>
                                                    <option value="0">Giảng viên</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="card-body col-6">
                                        <div class="form-group">
                                            <label>Email:<span class="required">(*)</span></label>
                                            <input type="email" class="form-control" id="edit_email" name="edit_email" value="">

                                        </div>
                                        <div class="form-group">
                                            <label>Phone:</label>
                                            <input type="text" class="form-control" id="edit_phone" name="edit_phone" value="">

                                        </div>
                                        <div class="form-group mb-0">
                                            <label>Ghi chú:</label>
                                            <textarea class="form-control" name="edit_note" id="edit_note" style="height: 150px"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-right">
                                    <button class="btn btn-primary" for="formEditUser">Lưu</button>
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
