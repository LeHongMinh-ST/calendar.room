@extends('layouts.master')
@section('title','Quản lí phản ánh')
@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote-bs4.js"></script>

    <script !src="">
        $(document).ready(function() {
            $('#select_department').select2();

            @error('department_id')
            toastr.error('{{$errors->first("department_id") }}', 'Tạo mới thất bại')
            @enderror
            @error('name')
            toastr.error('{{$errors->first("name") }}', 'Tạo mới thất bại')
            @enderror
            $('summernote').summernote();
        });

    </script>
    <script>
        function uyen() {
            var content = document.getElementById('content').value;
            if(content.length==0){
                $('#validatecontent').text('Vui lòng nhập nội dung phản ánh');
            }else if (content.length<=10){

                $('#validatecontent').text('Vui lòng nhập nội dung phản ánh lớn hơn 10 kí tự');

            }else{
                $('#create').submit();
            }
        }
    </script>
@endsection
@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote-bs4.css" rel="stylesheet">
    <style>
        .jquery-validation-error {
            color: #ff0000;
            font-size: 11px;
            font-style: italic;
            font-weight: normal;
        }
        .note-toolbar button{
            color: black !important;
            border: 1px solid #c1bdbd !important;
            margin: 5px !important;
        }
        .select2-selection__rendered{
            line-height: 40px !important;
        }
        .red{
            color: red !important;
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
                    <div class="breadcrumb-item">Thêm mới phản ánh</div>
                </div>
            </div>
            <div class="section-body">
                <h2 class="section-title">Thêm mới phản ánh</h2>


                <div class="row">
                    <div class="col-12">
                        <div class="card">
{{--                            <div class="card-header">--}}
{{--                                <a href="{{route('feedback.index')}}" class="btn btn-success" style="border-radius: 5px!important;">Danh sách phản ánh</a>--}}

{{--                            </div>--}}
                            <div class="card-body">
                                <form action="{{route('feedback.store')}}" method="POST" id="create">
                                    @csrf
                                    <div class="form-group row mb-4">
                                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Phòng máy</label>
                                        <div class="col-sm-12 col-md-7">
                                            <select class="form-control selectric" name="room_id" id="select_department">
                                                @foreach($rooms as $room)
                                                    <option value="{{$room['id']}}">{{$room['name']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row mb-4">
                                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Nội dung phản ánh (<span class="required red">*</span>)</label>
                                        <div class="col-sm-12 col-md-7">
                                            <textarea type="text" class="form-control summernote" name="content" id="content"  value="{{old('content')}}"></textarea>
                                    <p id="validatecontent" style="color: red"></p>
                                        </div>
                                    </div>
                                    <div class="form-group row mb-4">
                                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                                        <div class="col-sm-12 col-md-7">
                                            <a class="btn btn-primary" onclick="uyen()">Thêm mới</a>
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
