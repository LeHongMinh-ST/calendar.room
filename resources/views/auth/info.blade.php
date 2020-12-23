@extends('layouts.master');
@section('script')
    <script src="{{asset('assets/js/info.js')}}"></script>
@endsection

@section('content')
<div class="main-content">
    <section class="section">
      <div class="section-header">
        <h1>Thông tin người dùng</h1>
        <div class="section-header-breadcrumb">
          <div class="breadcrumb-item active"><a href="/">Trang chủ</a></div>
          <div class="breadcrumb-item">Thông tin cá nhân</div>
        </div>
      </div>
      <div class="section-body">
        <h2 class="section-title">Xin chào, {{ Auth::user()->full_name }}!</h2>
        <p class="section-lead">
          Chỉnh sửa thông tin của bạn ở trang này
        </p>

        <div class="row mt-sm-4">
          <div class="col-12 col-md-12 col-lg-5">
            <div class="card profile-widget">
              <div class="profile-widget-header">
                <img alt="image" src="../assets/img/avatar/avatar-1.png" class="rounded-circle profile-widget-picture">
              </div>
              <div class="profile-widget-description">
                <div class="profile-widget-name">{{ Auth::user()->full_name  }} <div class="text-muted d-inline font-weight-normal"><div class="slash"></div>@if(Auth::user()->role == 1) Quản lý @elseif(Auth::user()->role == 2) Kĩ thuật viên @else Giảng viên @endif</div></div>
                {{ Auth::user()->note }}
              </div>
            </div>
          </div>
          <div class="col-12 col-md-12 col-lg-7">
            <div class="card">
              <form id='formEditInfo'>
                <div class="card-header">
                  <h4>Chỉnh sửa thông tin</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                      <div class="form-group col-md-12 col-12">
                        <label>Họ và tên</label>
                        <input type="text" class="form-control" name="full_name" value="{{ Auth::user()->full_name }}">
                      </div>
                    </div>
                    <div class="row">
                      <div class="form-group col-md-7 col-12">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" value="{{ Auth::user()->email }}">
                      </div>
                      <div class="form-group col-md-5 col-12">
                        <label>Số điện thoại</label>
                        <input type="tel" class="form-control" name="phone" value="{{ Auth::user()->phone }}">
                      </div>
                    </div>
                    <div class="row">
                      <div class="form-group col-12">
                        <label>Ghi chú</label>
                        <textarea class="form-control summernote-simple" name="note" rows="10" style="height: 100%" >{{ Auth::user()->note}}</textarea>
                      </div>
                    </div>
                </div>
                <div class="card-footer text-right">
                  <button class="btn btn-primary" for="formEditInfo">Lưu</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
@endsection