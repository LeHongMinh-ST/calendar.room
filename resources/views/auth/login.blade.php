@extends('layouts.auth')
@section('title')
    {{__('Đăng nhập')}}
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
    @if(\Session::has('login_error'))
    <script>
        toastr.error('{{\Session::pull('login_error')}}', 'Đăng nhập thất bại')
    </script>
    @endif
@endsection
@section('content')
    <section class="section">
        <div class="container mt-5">
            <div class="row">
                <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
                    <div class="login-brand">
                        <img src="{{asset('/')}}assets/img/FITA.png" alt="logo" width="100"
                             class="shadow-light rounded-circle">
                    </div>

                    <div class="card card-primary">
                        <div class="card-header"><h4>Đăng nhập</h4></div>

                        <div class="card-body">
                            <form method="POST" action="{{route('login')}}" class="needs-validation">
                                @csrf
                                <div class="form-group">
                                    <label for="email">Tài khoản</label>
                                    <input id="name" type="text" class="form-control" name="user_name" tabindex="1"
                                           required autofocus value="{{old('user_name')}}">
                                </div>

                                <div class="form-group">
                                    <div class="d-block">
                                        <label for="password" class="control-label">Mật khẩu</label>
                                    </div>
                                    <input id="password" type="password" class="form-control" name="password"
                                           tabindex="2" required>
                                    <div class="invalid-feedback">
                                        Vui lòng điền mật khẩu
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                                        Đăng nhập
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
