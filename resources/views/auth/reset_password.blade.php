@extends('layouts.auth')
@section('title')
    {{__('Đổi mật khẩu')}}
@endsection
@section('css')
    <style>
        .error-feedback {
            width: 100%;
            margin-top: .25rem;
            font-size: 80%;
            color: #dc3545;
        }
    </style>
@endsection
@section('script')
  <script>
    @if(session('message'))
        toastr.error(@json(session('message')));
    @endif
  </script>
 
  <script src="{{ asset('assets/js/reset.js') }}"></script>
@endsection
@section('content')
<section class="section">
      <div class="container mt-5">
        <div class="row">
          <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
            <div class="login-brand">
              <img src="{{asset('/')}}assets/img/FITA.png" alt="logo" width="100" class="shadow-light rounded-circle">
            </div>

            <div class="card card-primary">
              <div class="card-header"><h4>Đổi mật khẩu</h4></div>

              <div class="card-body">
                <form action="{{ route('profile.update-password') }}" method="POST" id="resetForm">
                  @csrf
                  @method('put')
                  <div class="form-group">
                    <label for="email">Mật khẩu cũ</label>
                    <input id="old_password" type="password" class="form-control" name="old_password" tabindex="1">
                  </div>

                  <div class="form-group">
                    <label for="password">Mật khẩu mới</label>
                    <input id="password" type="password" class="form-control"  name="password" >
                  </div>

                  <div class="form-group">
                    <label for="password_confirm">Xác nhận lại mật khẩu</label>
                    <input id="password_confirm" type="password" class="form-control" name="confirm_password">
                  </div>

                  <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-lg btn-block" id="btnSubmitForm" tabindex="4">
                      Cập nhật
                    </button>
                  </div>
                </form>
              </div>
            </div>
            <div class="simple-footer">
                Copyright &copy; 2020 Bản quyền của Bộ Môn CNPM phát triển bởi nhóm ST
            </div>
          </div>
        </div>
      </div>
    </section>
@endsection