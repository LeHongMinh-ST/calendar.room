@extends('layouts.master')
@section('title','Quản lí bộ môn')
@section('script')
    <script !src="">
        $(document).ready(function() {
            $('#select_department').select2();

            @error('department_id')
            toastr.error('{{$errors->first("department_id") }}', 'Tạo mới thất bại')
            @enderror
            @error('name')
            toastr.error('{{$errors->first("name") }}', 'Tạo mới thất bại')
            @enderror

        });
    </script>
@endsection
@section('css')
@endsection
@section('content')
    <div class="main-content">
        <div class="section">
            <div class="section-header">
                <h1>Bộ môn</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Trang chủ</a></div>
                    <div class="breadcrumb-item"><a href="#">Quản lí bộ môn</a></div>
                    <div class="breadcrumb-item">Thêm mới bộ môn</div>
                </div>
            </div>
            <div class="section-body">
                <h2 class="section-title">Thêm mới bộ môn</h2>


                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <a href="{{route('department.index')}}" class="btn btn-success" style="border-radius: 5px!important;">Danh sách bộ môn</a>

                            </div>
                            <div class="card-body">
                                <form action="{{route('department.store')}}" method="POST" class="needs-validation" novalidate="">
                                    @csrf
                                    <div class="form-group row mb-4">
                                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Mã bộ môn</label>
                                        <div class="col-sm-12 col-md-7">
                                            <input type="text" class="form-control" name="department_id" required autofocus value="{{old('department_id')}}">
                                            <div class="invalid-feedback">
                                                Vui lòng điền mã bộ môn
                                            </div>
                                        </div>

                                    </div>
                                    <div class="form-group row mb-4">
                                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Tên bộ môn</label>
                                        <div class="col-sm-12 col-md-7">
                                            <input type="text" class="form-control" name="name" required autofocus value="{{old('name')}}">
                                            <div class="invalid-feedback">
                                                Vui lòng điền tên bộ môn
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row mb-4">
                                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                                        <div class="col-sm-12 col-md-7">
                                            <button class="btn btn-primary" type="submit">Thêm mới</button>
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


    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="" id="myform" method="post">
                    {{ csrf_field() }}
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
                                <select name="department_id" id="add_department_id" class="form-control" required="required">
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">hủy</button>
                        <button type="submit" class="btn btn-primary add-save">Thêm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="" id="">
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
                                <select name="department_id" id="edit_department_id" class="form-control" required="required">

                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">hủy</button>
                        <button type="submit" class="btn btn-primary edit-save" value="">lưu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
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
                <style>
                    tr{height: 35px;}
                    tr td{font-size: 18px;padding-left: 15px;}
                    tr td a{font-size: 14px;}
                </style>
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
