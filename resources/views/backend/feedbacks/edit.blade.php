@extends('layouts.master')
@section('title','Quản lí phản ánh')
@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote-bs4.js"></script>

    <script !src="">
        $(document).ready(function() {
            $('#select_department').select2();

            @error('room_id')
            toastr.error('{{$errors->first("room_id") }}', 'cập nhật thất bại')
            @enderror
            @error('content')
            toastr.error('{{$errors->first("content") }}', 'cập nhật thất bại')
            @enderror
            $('.summernote').summernote();

        });
    </script>
    <script>
        function uyen() {
            var content = $('#content').val();
            console.log(content.length)
            if(content.length==0){
                toastr.error('Nội dung phản ánh không được để trống', 'Tạo mới thất bại');
            }else if (content.length<=10){
                toastr.error('Nội dung phản ánh không được ngắn dưới 10 kí tự', 'Tạo mới thất bại');
            }else{
                $('#update').submit();
            }
        }
        $('#submit').click( function(){
            uyen();
        });
    </script>
@endsection
@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote-bs4.css" rel="stylesheet">

@endsection
@section('content')
    <div class="main-content">
        <div class="section">
            <div class="section-header">
                <h1>Phản ánh</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="#">Trang chủ</a></div>
                    <div class="breadcrumb-item"><a href="#">Quản lí phản ánh</a></div>
                    <div class="breadcrumb-item">Chỉnh sửa phản ánh</div>
                </div>
            </div>
            <div class="section-body">
                <h2 class="section-title">Chỉnh sửa phản ánh</h2>


                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <a href="{{route('feedback.index')}}" class="btn btn-success" style="border-radius: 5px!important;">Danh sách phản ánh</a>

                            </div>
                            <div class="card-body">
                                <form action="{{route('feedback.update',$feedback->id)}}" method="Post" class="needs-validation" novalidate="" id="update">
                                    @csrf
                                    @method('put')
                                    <div class="form-group row mb-4">
                                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Phòng máy</label>
                                        <div class="col-sm-12 col-md-7">
                                            <select class="form-control selectric" name="room_id" id="select_room">
                                                @foreach($rooms as $room)
                                                    <option value="{{$room['id']}}">{{$room['name']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row mb-4">
                                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Nội dung phản ánh <span class="required">(*)</span></label>
                                        <div class="col-sm-12 col-md-7">
                                            <textarea type="text" class="form-control summernote" id="content" name="content" required autofocus >{!! $feedback->content !!}</textarea>
                                            <div class="invalid-feedback">
                                                Vui lòng điền nội dung phản ánh
                                            </div>
                                        </div>
                                    </div>
                                </form>

                                <div class="form-group row mb-4">
                                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                                        <div class="col-sm-12 col-md-7">
                                            <button class="btn btn-primary" id="submit" for="update">Cập nhật</button>
                                        </div>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
