@extends('layouts.master')
@section('title')
    {{__('Semester')}}
@endsection
@section('css')
    <link rel="stylesheet" href={{ asset('assets/css/style.css') }}>

    <link rel="stylesheet" type="text/css" media="screen" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/base/jquery-ui.css">
    <link href="https://adminlte.io/themes/AdminLTE/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet"/>
@endsection

@section('script')
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.1/jquery.js"></script>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/jquery-ui.min.js"></script>
    <script src="https://adminlte.io/themes/AdminLTE/bower_components/jquery/dist/jquery.min.js"></script>
    <script src="https://adminlte.io/themes/AdminLTE/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
    <script src="https://adminlte.io/themes/AdminLTE/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="bootstrap-datepicker.XX.js" charset="UTF-8"></script>

{{--    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>--}}
{{--    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>--}}
{{--    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>--}}
{{--    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />--}}

{{--    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" >--}}
{{--    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" ></script>--}}
{{--    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" ></script>--}}
{{--    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" ></script>--}}

    <script>
        $('#datepicker input').each(function () {
            $(this).datepicker({
                autoclose: true,
                format: "yyyy-mm-dd",
            });
        });
            // $('#datepicker input').daterangepicker({
            //     singleDatePicker: true,
            //     showDropdowns: true,
            //     format: "yyyy-mm-dd",
            //     minYear: 1901,
            //     maxYear: parseInt(moment().format('YYYY'),10)
            // });

    </script>

    <script !src="">
        $(document).ready(function() {
            @error('number_weeks')
            toastr.error('{{$errors->first("number_weeks") }}', 'Sửa thông tin thất bại')
            @enderror
            @error('start_year')
            toastr.error('{{$errors->first("start_year") }}', 'Sửa thông tin thất bại')
            @enderror
            @error('end_year')
            toastr.error('{{$errors->first("end_year") }}', 'Sửa thông tin thất bại')
            @enderror
            @error('semester_start_date')
            toastr.error('{{$errors->first("semester_start_date") }}', 'Sửa thông tin thất bại')
            @enderror
            @if(\Session::has('error'))
            toastr.error('{{\Session::pull('error')}}', 'Sửa thông tin thất bại');
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
                    <div class="breadcrumb-item">Sửa thông tin học kỳ</div>
                </div>
            </div>

            <div class="section-body">
                <h2 class="section-title">Sửa thông tin học kỳ</h2>
                <div class="row">
                    <div class="col-12 ">
                        <div class="card">
                            <form action="{{ route('backend.semester.update',$data['id'])}}" method="post" >
                                @csrf
                                @method('put')
                                <div class="card-header">
                                    <h4>Sửa thông tin học kỳ</h4>
                                </div>
                                <div class="card-body ">
                                    <div class="form-group">
                                        <div class="form-group row mb-4">
                                            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Học kỳ</label>
                                            <div class="col-sm-12 col-md-7">
                                                <select class="form-control" name="semester">
                                                    <option @if($data['semester'] == 1) selected="selected" @endif value="1">1</option>
                                                    <option @if($data['semester'] == 2) selected="selected" @endif value="2">2</option>
                                                    <option @if($data['semester'] == 3) selected="selected" @endif value="3">3</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="valid-feedback">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="form-group row mb-4">
                                            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Số tuần</label>
                                            <div class="col-sm-12 col-md-7">
                                                <input type="text" class="form-control " name="number_weeks" required="" value="{{$data['number_weeks']}}"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="form-group row mb-4 ">
                                            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Năm học</label>
                                            <div class="col-sm-12 col-md-7">
                                                <select class="form-control" name="school_year">
                                                    <option @if($data['school_year'] == --$data['start_year'] ." - ". ++$data['start_year']) selected="selected" @endif value="{{ --$data['start_year'] ." - ". ++$data['start_year']}}">{{ --$data['start_year'] ." - ". ++$data['start_year']}}</option>
                                                    <option @if($data['school_year'] == $data['start_year'] ." - ". ++$data['start_year']) selected="selected" @endif value="{{  --$data['start_year'] ." - ". ++$data['start_year']}}">{{ --$data['start_year'] ." - ". ++$data['start_year']}}</option>
                                                    <option @if($data['school_year'] == $data['start_year'] ." - ". ++$data['start_year']) selected="selected" @endif value="{{ --$data['start_year'] ." - ". ++$data['start_year']}}">{{ --$data['start_year'] ." - ". ++$data['start_year']}}</option>
                                                    <option @if($data['school_year'] == $data['start_year'] ." - ". ++$data['start_year']) selected="selected" @endif value="{{ --$data['start_year'] ." - ". ++$data['start_year']}}">{{  --$data['start_year'] ." - ". ++$data['start_year']}}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="form-group row mb-4" id="datepicker">
                                            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Ngày bắt đầu</label>
                                            <div class="col-sm-12 col-md-7">
                                                <input type="text" class="form-control " name="semester_start_date" required="" value="{{$data['semester_start_date']}}"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row mb-4">
                                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                                        <div class="col-sm-12 col-md-7">
                                            <input class="btn btn-primary" value="Sửa" type="submit">
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
