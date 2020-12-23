@extends('layouts.master')
@section('title')
    {{__('Danh sách tuần')}}
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
        .errors {
            color: red;
            /* background-color: #acf; */
        }


    </style>

@endsection
@section('script')
    <script src="{{asset('assets/js/week.js')}}"></script>

@endsection

@section('content')
    <input type="text" style="display: none" id="data-id" value="{{$semester_id}}">
    <div class="main-content">
        <section class="section">

            <div class="section-header">
                <h1>Học kỳ</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{route('backend.dashboard')}}">Trang chủ</a></div>
                    <div class="breadcrumb-item"><a href="{{route('backend.semester.index')}}">QLTT chung</a></div>
                    <div class="breadcrumb-item">Danh sách tuần</div>
                </div>
            </div>
            <div class="section-body">
                <h2 class="section-title">Danh sách tuần</h2>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                    <table class="table table-striped" id="listWeek">
                                        <thead>
                                        <tr >
                                            <th >Tuần</th>
                                            <th >Từ ngày</th>
                                            <th >Đến ngày</th>
                                            <th style="width: 300px">Tùy chọn</th>
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

    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="" id="myform" method="post">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Thêm ghi chú tuần</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="exampleFormControlTextarea1">Ghi chú</label>
                            <textarea class="form-control" name="note" id="note" style="height: 150px"></textarea>
                            <p style="color: red;" class="error_at_form_edit errorName"></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                        <button type="button" class="btn btn-primary btn-save">Thêm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal  fade" id="showModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header" style="padding: 25px">
                    <h4 class="modal-title" id="exampleModalLongTitle" style="padding-left: 13px;">Thông tin tuần</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div  class="col-sm-12" style="padding-left: 35px;padding-right: 38px;">
                    <table class="table table-striped">
                        <tr>
                            <td style="width: 30%">Tuần:</td>
                            <td id="week"></td>
                        </tr>
                        <tr >
                            <td >Ngày bắt đầu học kỳ: </td>
                            <td id="start_day"></td>
                        </tr>

                        <tr >
                            <td >Ngày kết thúc học kỳ:</td>
                            <td id="end_day"></td>
                        </tr>

                        <tr>
                            <td class="td">Ghi chú:</td>
                            <td id="note1" ></td>
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
