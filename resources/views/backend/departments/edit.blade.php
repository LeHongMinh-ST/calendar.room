@extends('layouts.master')
@section('title','Quản lí bộ môn')
@section('script')
    <script !src="">
        $(document).ready(function() {
            $('#select_department').select2();

            @error('department_id')
            toastr.error('{{$errors->first("department_id") }}', 'Chỉnh sửa thất bại')
            @enderror
            @error('name')
            toastr.error('{{$errors->first("name") }}', 'Chỉnh sửa thất bại')
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
                <h1>Môn học</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Trang chủ</a></div>
                    <div class="breadcrumb-item"><a href="#">Quản lí bộ môn</a></div>
                    <div class="breadcrumb-item">Chỉnh sửa bộ môn</div>
                </div>
            </div>
            <div class="section-body">
                <h2 class="section-title">Chỉnh sửa bộ môn</h2>


                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <a href="{{route('department.index')}}" class="btn btn-success" style="border-radius: 5px!important;">Danh sách bộ môn</a>

                            </div>
                            <div class="card-body">
                                <form action="{{route('department.update',$department->id)}}" method="post" class="needs-validation" novalidate="">
                                    @csrf
                                    @method('put')
                                    <div class="form-group row mb-4">
                                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Mã bộ môn</label>
                                        <div class="col-sm-12 col-md-7">
                                            <input type="text" class="form-control" name="department_id" required autofocus value="{{$department->department_id}}">
                                            <div class="invalid-feedback">
                                                Vui lòng điền mã bộ môn
                                            </div>
                                        </div>

                                    </div>
                                    <div class="form-group row mb-4">
                                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Tên môn học</label>
                                        <div class="col-sm-12 col-md-7">
                                            <input type="text" class="form-control" name="name" required autofocus value="{{$department->name}}" >
                                            <div class="invalid-feedback">
                                                Vui lòng điền tên bộ môn
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row mb-4">
                                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                                        <div class="col-sm-12 col-md-7">
                                            <button class="btn btn-primary" type="submit">Lưu</button>
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
