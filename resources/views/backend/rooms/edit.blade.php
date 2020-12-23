@extends('layouts.master')
@section('title')
    {{__('Room')}}
@endsection
@section('css')
    <link rel="stylesheet" href={{ asset('assets/css/style.css') }}>
    <style>
        .required{
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
    <script>
        $(document).ready(function() {
            $("#select2").select2();
        });
    </script>
    <script !src="">
        $(document).ready(function() {
            @error('room_id')
            toastr.error('{{$errors->first("room_id") }}', 'Tạo mới thất bại')
            @enderror
            @error('name')
            toastr.error('{{$errors->first("name") }}', 'Tạo mới thất bại')
            @enderror
            @error('status')
            toastr.error('{{$errors->first("status") }}', 'Tạo mới thất bại')
            @enderror
            @error('computer_number')
            toastr.error('{{$errors->first("computer_number") }}', 'Tạo mới thất bại')
            @enderror
            @error('address')
            toastr.error('{{$errors->first("address") }}', 'Tạo mới thất bại')
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
                    <div class="breadcrumb-item">Sửa thông tin phòng máy</div>
                </div>
            </div>

            <div class="section-body">
                <h2 class="section-title">Sửa thông tin phòng máy</h2>
                <div class="row">
                    <div class="col-12 ">
                        <div class="card">
                            <form class="needs-validation" novalidate="" action={{ route('backend.room.update',$data['id']) }} method="POST"  >
                                @csrf
                                @method('put')

                                <div class="card-header">
                                    <h4>Thêm mới</h4>
                                </div>
                                <div class="card-body ">
                                    <div class="form-group">
                                        <div class="form-group row mb-4">
                                            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Mã phòng máy<span class="required">(*)</span></label>
                                            <div class="col-sm-12 col-md-7">
                                                <input type="text" class="form-control" name="room_id" required autofocus  value="{{$data['room_id']}}"/>
                                                <div class="invalid-feedback">
                                                    Bạn chưa nhập mã phòng máy
                                                </div>
                                            </div>
                                        </div>
                                        <div class="valid-feedback">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="form-group row mb-4">
                                            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Tên phòng máy<span class="required">(*)</span></label>
                                            <div class="col-sm-12 col-md-7">
                                                <input type="text" class="form-control" name="name" required autofocus  value="{{$data['name']}}"/>
                                                <div class="invalid-feedback">
                                                    Bạn chưa nhập tên phòng máy
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="form-group row mb-4 ">
                                            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Tình trạng<span class="required">(*)</span></label>
                                            <div class="col-sm-12 col-md-7">
                                                <input type="text" class="form-control" name="status" required autofocus  value="{{$data['status']}}"/>
                                                <div class="invalid-feedback">
                                                    Bạn chưa nhập tình trạng máy
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="form-group row mb-4" id="datepicker">
                                            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Số lượng máy<span class="required">(*)</span></label>
                                            <div class="col-sm-12 col-md-7">
                                                <input type="text" class="form-control " name="computer_number" required autofocus value="{{$data['computer_number']}}"/>
                                                <div class="invalid-feedback">
                                                    Bạn chưa nhập số máy
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="form-group row mb-4" id="datepicker">
                                            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Địa chỉ<span class="required">(*)</span></label>
                                            <div class="col-sm-12 col-md-7">
                                                <input type="text" class="form-control " name="address" required autofocus value="{{$data['address']}}"/>
                                                <div class="invalid-feedback">
                                                    Bạn chưa nhập địa chỉ
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="form-group row mb-4" id="datepicker"><label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Môn học<span class="required">(*)</span></label>
                                            <div class="col-sm-12 col-md-7">
                                                <select class="form-control " id="select2" multiple name="subjects[]">
                                                    @foreach($listSubject as $option)
                                                        @if(in_array($option->name, $data['listSubject'])) <option value="{{$option->name}}" class="tags" selected >{{$option->name}}</option>
                                                        @else <option value="{{$option->name}}" >{{$option->name}}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback">
                                                    Bạn chưa nhập tên
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="form-group row mb-4" id="datepicker">
                                            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Phần mềm</label>
                                            <div class="col-sm-12 col-md-7">
                                                <input type="text" class="form-control " name="software" autofocus value="{{$data['software']}}"/>
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
