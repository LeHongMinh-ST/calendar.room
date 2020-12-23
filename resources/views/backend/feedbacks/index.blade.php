@extends('layouts.master')
@section('title','Quản lí phản ánh')
@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote-bs4.js"></script>
    <script src="{{asset('assets/js/feedback.js')}}"></script>
    <script !src="">
        $(document).ready(function() {
            $('#select_room').select2();

            @error('department_id')
            toastr.error('{{$errors->first("department_id") }}', 'Tạo mới thất bại')
            @enderror
            @error('name')
            toastr.error('{{$errors->first("name") }}', 'Tạo mới thất bại')
            @enderror
            $('summernote').summernote();
        });

    </script>

@endsection
@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote-bs4.css" rel="stylesheet">
    <style>
        .select2-selection, .select2-selection--single{
            padding-top: 5px !important;
        }
    </style>

@endsection
@section('content')
    <div class="main-content">
        <div class="section">
            <div class="section-header">
                <h1>Phản ánh</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Trang chủ</a></div>
                    <div class="breadcrumb-item"><a href="#">Quản lí phản ánh</a></div>
                    <div class="breadcrumb-item">Danh sách phản ánh</div>
                </div>
            </div>
            <div class="section-body">
                <h2 class="section-title">Danh sách phản ánh</h2>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="row">
                                <div class="col-12">
                                            <div class="card" style="width: 100%; border-radius: 5px; ">
                                                <div class="card-header" style=" background-color: #f5f5f5;min-height: 10px; height: 40px;">
                                                    <h4 class="card-title" style="color: #6c757d; "><i class="fas fa-search"></i> Lọc phản ánh</h4>
                                                </div>
                                                <div class="card-body">
                                                    <form class="form-inline" action="" method="post" id="formFilterFeedback">
                                                        @csrf
                                                        <div class="form-group col-4" >
                                                            <select name="filterStatus" id="filterStatus" class="custom-select">
                                                                <option value="0" > Chưa xử lí</option>
                                                                <option value="1" > Đã xử lí</option>
                                                                <option value="2" > Đã gỡ</option>

                                                            </select>
                                                        </div>
                                                        <div class="form-group col-4">
                                                            <select name="filterRoom" id="filterRoom">
                                                                <option value="">Tất cả các phòng</option>
                                                                @foreach($rooms as $room)
                                                                    <option value="{{$room['id']}}">{{$room['name']}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="form-group col-4">
                                                            <button class="btn btn-primary mb-2 filter"  style="margin-right: 5px"><i class="fas fa-search "></i> Lọc dữ liệu</button>
                                                            <button class="btn btn-danger mb-2 reset" id="resetFilter"><i class="fas fa-undo-alt "></i> Đặt lại</button>
                                                        </div>
                                                    </form>
                                                </div>


                                    </div>
                                </div></div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="listFeedback">
                                        <thead>
                                        <tr>
                                            <th>
                                                STT
                                            </th>
                                            <th>
                                                Phòng máy
                                            </th>
                                            <th>
                                                Ngày phản ánh
                                            </th>
                                            <th>
                                                Người phản ánh
                                            </th>
                                            <th>
                                                Tình trạng
                                            </th>
                                            <th>
                                                Hành động
                                            </th>

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
        <div class="modal fade " id="showModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
             aria-hidden="true">
            <style>
                .content img{
                    width: 100% !important;
                }
            </style>
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content ">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Thông tin phản ánh</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div  class="col-sm-12">
                            <div class="room"></div>
                            <div class="content">
                            </div>
                            <div   style="padding: 10px">
                                <p id="content_show" style="border: .05px solid #0000004a;
                                            padding: 10px;
                                            background-color: #e1eaea;
                                            border-radius: 9px;"></p>
                            </div>
                            <div class="user"></div>
                            <div class="date"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div  id="button"></div>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">đóng</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade " id="hahdle" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
             aria-hidden="true">
            <style>
                .content img{
                    width: 100% !important;
                }
            </style>
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content " style="text-align: center">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Xử lí phản ánh</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <form action="{{route('feedback.handle.store')}}" method="POST" id="create_handle">
                        @csrf
                        <input type="text" name="feedback_id" id="feedback_id" style="display: none">
                        <div class="form-group row mb-4">
                            <label class="col-form-label text-md-right col-12 col-md-6 col-lg-6" style="left: -50px;!important;">Mô tả xử lí phản ánh <span class="required" style="color: red; ">(*)</span></label>
                            <div class="col-sm-12 col-md-10" style="margin: 0 auto">
                                <style>
                                    .note-btn{
                                        color: black !important;
                                    }
                                </style>
                                <input type="text" class="form-control summernote" name="description" id="description">
                                <p id="validate-handle" style="color: red"></p>
                            </div>
                        </div>
                        <div class="form-group row mb-4" >
                            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                            <div class="col-sm-12 col-md-7" style="text-align: right !important;
    right: -44px; !important;">
                                <a class="btn btn-primary add-handle">xác nhận xử lí</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade " id="showHandle" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
             aria-hidden="true">
            <style>
                .content img{
                    width: 100% !important;
                }
            </style>
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content ">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Thông tin xử lí phản ánh</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div style="height: 300px;" class="col-sm-12">
                        <div class="date"></div>
                        <div class="content"></div>
                        <div class="user"></div>
                        <div class="date"></div>

                    </div>
                    <div class="modal-footer" id="button">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">đóng</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
