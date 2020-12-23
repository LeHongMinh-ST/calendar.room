@extends('layouts.master')
@section('title')
    {{__('Semester')}}
@endsection
@section('css')
    <link rel="stylesheet" href={{ asset('assets/css/style.css') }}>

    <link rel="stylesheet" type="text/css" media="screen" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/base/jquery-ui.css">
    <link href="https://adminlte.io/themes/AdminLTE/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet"/>
    <style>

    </style>
@endsection

@section('script')
    <script src="https://adminlte.io/themes/AdminLTE/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

    <script>
        $('#datepicker input').each(function () {
            $(this).datepicker({
                autoclose: true,
                format: "yyyy-mm-dd",
            });
        });
    </script>

    <script !src="">
        $(document).ready(function() {
            $("#checkValidate").validate({
                onfocusout: false,
                onkeyup: false,
                onclick: false,
                onchange: false,
                rules: {
                    "number_weeks": {
                        required: true,
                        min: 5
                    },
                    "semester_start_date": {
                        required: true,
                    }
                },
                messages: {
                    "number_weeks": {
                        required: function(){
                            toastr.remove();
                            toastr.error('không được để trống số tuần','Tạo mới thất bại')
                        },
                        min: function(){
                            toastr.remove();
                            toastr.error('Số tuần phải lớn hơn 5 ','Tạo mới thất bại')
                        },
                    },
                    "semester_start_date": {
                        required: function(){
                            toastr.remove();
                            toastr.error('không được để trống ngày bắt đầu học kỳ','Tạo mới thất bại')
                        },
                    }
                },
            });

            @error('number_weeks')
            toastr.error('{{$errors->first("number_weeks") }}', 'Tạo mới thất bại')
            @enderror
            @error('start_year')
            toastr.error('{{$errors->first("start_year") }}', 'Tạo mới thất bại')
            @enderror
            @error('end_year')
            toastr.error('{{$errors->first("end_year") }}', 'Tạo mới thất bại')
            @enderror
            @error('semester_start_date')
            toastr.error('{{$errors->first("semester_start_date") }}', 'Tạo mới thất bại')
            @enderror
            @if(\Session::has('error'))
            toastr.error('{{\Session::pull('error')}}', 'Tạo mới thất bại');
            @endif
        });
    </script>
@endsection

@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Học kỳ</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{route('backend.dashboard')}}">Trang chủ</a></div>
                    <div class="breadcrumb-item"><a href="{{route('backend.semester.index')}}">QLTT chung</a></div>
                    <div class="breadcrumb-item">Thêm mới</div>
                </div>
            </div>

            <div class="section-body">
                <h2 class="section-title">Thêm mới học kỳ</h2>
                <div class="row">
                    <div class="col-12 ">
                        <div class="card">
                            <form class="" novalidate="" id="checkValidate" action={{ route('backend.semester.store') }} method="POST"  >
                                {{csrf_field()}}
                                <div class="card-header">
                                    <h4>Thêm mới</h4>
                                </div>
                                <div class="card-body ">
                                    <div class="form-group">
                                        <div class="form-group row mb-4">
                                            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Học kỳ</label>
                                            <div class="col-sm-12 col-md-7">
                                                <select class="form-control" name="semester">
                                                    <option value="1">1</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="valid-feedback">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="form-group row mb-4">
                                            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Số tuần<span class="">(*)</span></label>
                                            <div class="col-sm-12 col-md-7">
                                                <input type="text" class="form-control" name="number_weeks"   value="{{old('number_weeks')}}"/>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="form-group row mb-4 ">
                                            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Năm học<span class="required">(*)</span></label>
                                            <div class="col-sm-12 col-md-7">
                                                <select class="form-control" name="school_year">
                                                    <option value="{{ --$yearNow ." - ". ++$yearNow}}">{{ --$yearNow ." - ". ++$yearNow}}</option>
                                                    <option value="{{$yearNow ." - ". ++$yearNow}}">{{ --$yearNow ." - ". ++$yearNow}}</option>
                                                    <option value="{{$yearNow ." - ". ++$yearNow}}">{{ --$yearNow ." - ". ++$yearNow}}</option>
                                                    <option value="{{$yearNow ." - ". ++$yearNow}}">{{  --$yearNow ." - ". ++$yearNow}}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="form-group row mb-4" id="datepicker">
                                            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Ngày bắt đầu<span class="required">(*)</span></label>
                                            <div class="col-sm-12 col-md-7">
                                                <input type="text" class="form-control " name="semester_start_date"  value="{{old('semester_start_date')}}"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row mb-4">
                                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                                        <div class="col-sm-12 col-md-7">
                                            <input class="btn btn-primary" value="Thêm" type="submit">
                                        </div>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

@endsection
