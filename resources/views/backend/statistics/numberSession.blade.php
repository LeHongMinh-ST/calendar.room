@extends('layouts.master')
@section('title')
    {{__('Thống kê số tiết thực hành')}}
@endsection
@section('css')
    <link rel="stylesheet" href={{ asset('assets/css/style.css') }}>
    <style>
        .td{
            position: relative;
        }
        .p{
            position: absolute;
            top:0;
        }
        .form_time_assingment p{
            margin-left: 213px;
        }
        .select2-selection__rendered {
            padding-top: 6px;
        }
        .messageCannotCreate{
            background-color: #f2dede;
            color: #a94442;
            width: 100%
        }

        .table tfoot {
            background-color: #455a64;
            color: azure;
        }
        form#formFilter {
            align-items: unset;
        }

        form#formFilter .form-group {
            align-items: unset;
        }

        label#filterRoom-error {
            justify-content: unset;
        }
        label#filterYear-error {
            justify-content: unset;
        }
        #statisticsSession th{
            padding: .75em;
            height: 20px !important;
        }
    </style>
{{--    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.min.js"></script>--}}

@endsection
@section('script')
    <script src="https://cdn.datatables.net/plug-ins/1.10.20/api/sum().js"></script>

    <script src="{{asset('assets/js/statistics.js')}}"></script>
@endsection

@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Thống kê</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{route('backend.dashboard')}}">Trang chủ</a></div>
                    <div class="breadcrumb-item">Thống kê số tiết thực hành</div>
                </div>
            </div>
            <div class="section-body">
                <h2 class="section-title"> Thống kê số tiết của các nhóm thực hành trên phòng máy theo học kỳ. Năm học</h2>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card" style="width: 100%; border-radius: 5px; ">
                                        <div class="card-header" style=" background-color: #f5f5f5;min-height: 10px; height: 40px;">
                                            <h4 class="card-title" style="color: #6c757d; "><i class="fas fa-search"></i>Tùy chọn điều kiện thống kê</h4>
                                        </div>
                                        <div class="card-body">
                                            <form class="form-inline" action="" method="post" id="formFilter">
                                                @csrf
                                                <div class="form-group col-3">
                                                    <select name="filterYear" id="filterYear" class="select2-selection__rendered select2 select2-input">
                                                        <option value="">Vui lòng chọn năm học: </option>
                                                        @forelse($yearSchool as $year)
                                                            <option value="{{$year}}" >Năm học: {{$year}}</option>
                                                        @empty
                                                            <option value="Không có dữ liệu phòng máy " disabled></option>
                                                        @endforelse
                                                    </select>
                                                </div>
                                                <div class="form-group col-3" >
                                                    <select name="filterSemester" id="filterSemester" class="custom-select select2-selection__rendered">
                                                        @if(!empty($yearSchool))
                                                            <option value="">Tất cả học kỳ</option>
                                                        @else
                                                            <option value="">Chưa có dữ liệu học kì</option>
                                                        @endif
                                                    </select>
                                                </div>
                                                <div class="form-group col-3">
                                                    <select name="filterRoom" id="filterRoom" class="select2-selection__rendered select2 select2-input">
                                                        <option value="">Vui lòng chọn phòng máy</option>
                                                        @forelse($rooms as $room)
                                                            <option value="{{$room->id}}">{{$room->name}}</option>
                                                        @empty
                                                            <option value="Không có dữ liệu phòng máy " disabled></option>
                                                        @endforelse
                                                    </select>
                                                </div>
                                                <div class="form-group col-3">
                                                    <select name="filterFaculty" id="filterFaculty" class="select2-selection__rendered">
                                                        <option value="">Tất cả các khoa</option>
                                                        @forelse($faculties as $faculty)
                                                            <option value="{{$faculty->id}}">{{$faculty->name}}</option>
                                                        @empty
                                                            <option value="Không có dữ liệu phòng máy " disabled></option>
                                                        @endforelse
                                                    </select>
                                                </div>
                                                <div class="form-group col-3" style="padding-top:20px; float: right ">
                                                    <button class="btn btn-primary mb-2 filter" id="filter" for="formFilter" style="margin-right: 5px"><i class="fas fa-search "></i> Lọc dữ liệu</button>
                                                    <button class="btn btn-danger mb-2 reset" id="resetFilter"><i class="fas fa-undo-alt "></i> Đặt lại</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div></div>
                            <div class="card-body" id="body">
                                <h1 style="text-align: center" class="info-table"></h1>
                                <table class="table table-striped" id="statisticsSession">
                                    <thead>
                                    <tr>
                                        <th style="padding: .75rem">STT</th>
                                        <th >Mã MH</th>
                                        <th>Nhóm TH</th>
                                        <th>Tên lớp</th>
                                        <th>Sĩ số</th>
                                        <th>Số Tiết TH</th>
                                        <th >Số tiết</th>
                                    </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot >
                                    <tr>
                                        <th colspan="6" >Tổng số tiết</th>
                                        <th ></th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
