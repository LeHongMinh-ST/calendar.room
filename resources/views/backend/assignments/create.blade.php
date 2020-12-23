@extends('layouts.master')
@section('title')
    {{__('Room')}}
@endsection
@section('css')
    <link rel="stylesheet" href={{ asset('assets/css/style.css') }}>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>

    <style>
        .required {
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
    </style>
@endsection

@section('script')

{{--        <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>--}}
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script>
        $(document).ready(function () {
            $("#select2").select2();
            $('#select2-assignment').select2();
        });
    </script>


    <script type="text/javascript">
        $(document).ready(function () {
            $("#select2-assignment").change(function () {

                console.log($(this).val());
                if($(this).val() == "null"){
                    $('input[name="datefilter"]').prop("disabled",true);
                    $('input[name="datefilter"]').val('');
                    return;
                }

                let id_hk = $(this).val();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    dataType: '',
                    type: 'POST',
                    url: '/admin/assignment/getTimeOfSemester',
                    data: {
                        id_hk : id_hk
                    },
                    success: function (result)
                    {
                        console.log(result);
                        //Remove disable time input
                        $('input[name="datefilter"]').prop("disabled",false);

                        // console.log(result);
                        $('input[name="datefilter"]').daterangepicker({
                            minDate: result.semester_start_date,
                            maxDate: result.semester_end_date,
                            autoUpdateInput: false,
                            locale: {
                                cancelLabel: 'Clear'
                            }
                        });
                        $('input[name="datefilter"]').on('apply.daterangepicker', function (ev, picker) {
                            $(this).val('( '+picker.startDate.format('DD-MM-YYYY') + ' ) - ( ' + picker.endDate.format('DD-MM-YYYY')+' )');
                        });

                        // $('input[name="datefilter"]').on('cancel.daterangepicker', function (ev, picker) {
                        //     $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
                        //     $(this).val('');
                        // });
                    }
                });
            });
        });
    </script>

    <script !src="">
        $(document).ready(function () {
            @error('phone')
            toastr.error('{{$errors->first("room_id") }}', 'Tạo mới thất bại')
            @enderror
            @error('name')
            toastr.error('{{$errors->first("name") }}', 'Tạo mới thất bại')
            @enderror
            @error('datefilter')
            toastr.error('{{$errors->first("status") }}', 'Tạo mới thất bại')
            @enderror
            @if(\Session::has('error'))
            toastr.error('{{Session::pull('error')}}', 'Tạo mới thất bại');
            @endif
        });
    </script>
@endsection

@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Phòng máy</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{route('backend.dashboard')}}">Trang chủ</a></div>
                    <div class="breadcrumb-item"><a href="{{route('backend.room.index')}}">QLTT chung</a></div>
                    <div class="breadcrumb-item">Thêm mới phòng máy</div>
                </div>
            </div>

            <div class="section-body">
                <h2 class="section-title">Thêm mới phòng máy</h2>
                <div class="row">
                    <div class="col-12 ">
                        <div class="card">
                            <form class="needs-validation" novalidate=""
                                  action={{ route('backend.assignment.store') }} method="POST">
                                @csrf
                                <div class="card-header">
                                    <h4>Thêm mới</h4>
                                </div>
                                <div class="card-body ">
                                    <div class="form-group">
                                        <div class="form-group row mb-4">
                                            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">phòng
                                                máy<span class="required">(*)</span></label>
                                            <div class="col-sm-12 col-md-7">
                                                <select class="form-control selectric" name="room_id"
                                                        id="select2">
                                                    @foreach($rooms as $room)
                                                        <option  value="{{$room['id']}}">{{$room['name']}}</option>
                                                    @endforeach
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
                                                <input type="text" class="form-control" name="name" required autofocus
                                                       value="{{old('name')}}"/>
                                                <div class="invalid-feedback">
                                                    Bạn chưa nhập tên phòng máy
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="form-group row mb-4 ">
                                            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Học
                                                kỳ<span class="required">(*)</span></label>
                                            <div class="col-sm-12 col-md-7">
                                                <select class="form-control selectric" name="semester_id"
                                                        id="select2-assignment">
                                                    <option value="null">Chọn học kỳ</option>
                                                    @foreach($semesters as $semester)
                                                        <option
                                                            value="{{$semester['id']}}">{{'Học kỳ: '.$semester['semester'].' - Năm học: '.$semester['school_year']}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="form-group row mb-4" id="datepicker">
                                            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Thời
                                                gian trực<span class="required">(*)</span></label>
                                            <div class="col-sm-12 col-md-7" id="datepicker">
                                                <input type="text" class="form-control " name="datefilter" disabled required
                                                       autofocus value="" />
                                                <div class="invalid-feedback">
                                                    Bạn chưa nhập số máy
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="form-group row mb-4">
                                            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Số điện
                                                thoại<span class="required">(*)</span></label>
                                            <div class="col-sm-12 col-md-7">
                                                <input type="text" class="form-control " name="phone" required
                                                       autofocus value="{{old('phone')}}"/>
                                                <div class="invalid-feedback">
                                                    Bạn chưa nhập địa chỉ
                                                </div>
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



{{--<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>--}}
{{--<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>--}}
{{--<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>--}}
{{--<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />--}}
{{--<input type="text" name="datefilter" value="" />--}}

{{--<script type="text/javascript">--}}
{{--    $(function() {--}}


{{--        $('input[name="datefilter"]').daterangepicker({--}}
{{--            minDate: '06/24/2020',--}}
{{--            maxDate: '07/08/2020',--}}
{{--            autoUpdateInput: false,--}}
{{--            locale: {--}}
{{--                cancelLabel: 'Clear'--}}
{{--            }--}}
{{--        });--}}

{{--        $('input[name="datefilter"]').on('apply.daterangepicker', function(ev, picker) {--}}
{{--            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));--}}
{{--        });--}}

{{--        $('input[name="datefilter"]').on('cancel.daterangepicker', function(ev, picker) {--}}
{{--            $(this).val('');--}}
{{--        });--}}

{{--    });--}}
{{--</script>--}}





