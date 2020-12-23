<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
//    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function username()
    {
        return 'user_name';
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'user_name' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('user_name', $request->input('user_name'))->first();
        if ($user == null) {
            \Session::put('login_error', trans('auth.failed'));
            return back()->withInput();
        }
        if ($user->is_active == 1) {
            if (auth()->guard()->attempt(['user_name' => $request->input('user_name'), 'password' => $request->input('password'), 'deleted_at' => null])) {
                if ($user->role_id == 1) {
                    return redirect()->route('backend.dashboard');
                } else {
                    return redirect()->route('calendar');
                }
            }
            \Session::put('login_error', 'Tài khoản hoặc mật khẩu không chính xác');
            return back()->withInput();
        }
        \Session::put('login_error', 'Tài khoản tạm khóa');
        return back()->withInput();
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();
        return redirect('/login');
    }
}
