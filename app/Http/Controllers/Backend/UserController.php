<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $departments = Department::select('id', 'department_id', 'name','is_active')->where('is_active',1)->get()->toArray();
        return view('backend.users.index')->with('departments', $departments);
    }

    public function getData()
    {
        $users = User::select('id', 'user_name', 'email', 'full_name', 'phone', 'role_id', 'department_id', 'is_active')->orderBy('id', 'DESC')->get();
        return DataTables::of($users)
            ->editColumn('email',function ($user){
                if($user->email){
                    return $user->email;
                }

                return "Đang cập nhật";
            })
            ->editColumn('is_active', function ($user) {
                $string ='';
                if($user->role_id !=1)
                {
                    if($user->is_active == 1){
                        $string .=   '<form action="" method="post">
                                        <label class="switch">
                                            <input class="active_switch" type="checkbox" id="active_switch' . $user->id . '" checked="checked" data-id="' . $user->id . '">
                                            <span class="slider round"></span>
                                        </label>
                                    </form>';
                    }else{
                        $string .=   '<form action="" method="post">
                                        <label class="switch">
                                            <input class="active_switch" type="checkbox" id="active_switch' . $user->id . '" data-id="' . $user->id . '">
                                            <span class="slider round"></span>
                                        </label>
                                    </form>';
                    }
                }else{
                    $string .=  '<form action="" method="post">
                                    <label class="switch">
                                        <input class="active_switch" type="checkbox" id="active_switch' . $user->id . '" disabled checked="checked"">
                                        <span class="slider round"></span>
                                    </label>
                                </form>';
                }

                return $string;
            })
            ->editColumn('role_id', function ($user) {
                $role = Role::where('role_id', $user->role_id)->select('user_groups')->first()->toArray();
                return $role['user_groups'];
            })
            ->editColumn('department_id', function ($user) {
                $department = $user->Department()->where('is_active',1)->first();
                return $department ? $department['name'] : 'Đang cập nhật';
            })
            ->editColumn('phone', function ($user) {
                return ($user->phone) ? $user->phone : 'Đang cập nhật';
            })
            ->addColumn('action', function ($user) {
                $string = "";
                $string .= '<a href="#" class="btn btn-primary btn-icon btn-edit" data-id="' . $user->id . '" title="sửa người dùng"><i class="fas fa-edit"></i></a>';
                if ($user->role_id != 1) {
                    $string .= '<a href="#" class="btn btn-danger btn-delete" data-id="' . $user->id . '" title="xóa người dùng"><i class="fas fa-trash-alt"></i></a>';
                }
                return $string;
            })
            ->addIndexColumn()
            ->rawColumns(['is_active', 'action'])
            ->make();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            $validator = Validator::make($request->all(),
                [
                    'user_name' => 'required|max:10|alpha_num',
                    'full_name' => 'required|max:50',
                    'email'     => 'required|email|max:50',
                    'department'=> 'required',
                    'role'      => 'required'
                ]
            );

            if ($validator->fails()) {
                return false;
            }

            $checkUserName = User::select('user_name')->where(['user_name'=>$request->get('user_name')])->first();
            if(!empty($checkUserName)){
                return false;
            }

            $checkEmailUser = User::select('email')->where('email',$request->get('email'))->first();
            if(!empty($checkEmailUser)){
                return false;
            }

            $user = new User();
            $user->user_name        = $request->get('user_name');
            $user->full_name        = $request->get('full_name');
            $user->email            = $request->get('email');
            $user->phone            = $request->get('phone');
            $user->role_id          = $request->get('role');
            $user->department_id    = $request->get('department');
            $user->note             = $request->get('note');
            $user->user_create_id   = Auth::user()->id;
            $user->user_update_id   = Auth::user()->id;
            $user->password         = Hash::make(env('PASSWORD_USER',12345678));
            $user->is_active        = 1;

            $success                = $user->save();

            if ($success)
            {
                $message = 'Thêm mới tài khoản '. $user->user_name .' dùng thành công';
                return response()->json([
                    'error'     => false,
                    'message'   => $message,
                ]);
            }

        }catch(\Exception $e){
            $message = 'Thêm mới người dùng thất bại';
            return response()->json([
                'error'     => true,
                'message'   => $e->getMessage(),
            ]);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return response()->json([
            'error' =>  false,
            'user'  =>  $user,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try
        {
            // dd($request->all());

            $validator = Validator::make($request->all(),
                [
                    'user_name' => 'required|unique:users,user_name,' . $id . '|max:10|alpha_num',
                    'full_name' => 'required|max:50',
                    'email'     => 'required|email|unique:users,email,' . $id . '|max:50',
                    'department'=> 'required',
                    'role'      => 'required'
                ]
            );

            $user                   = User::findOrFail($id);
            $user->user_name        = $request->get('user_name');
            $user->full_name        = $request->get('full_name');
            $user->email            = $request->get('email');
            $user->phone            = $request->get('phone');
            $user->role_id          = $request->get('role');
            $user->department_id    = $request->get('department');
            $user->note             = $request->get('note');
            $user->user_update_id   = Auth::user()->id;

            $success = $user->save();

            if ($success)
            {
                $message = 'Cập nhật thành công tài khoản '. $user->user_name;
                return response()->json([
                    'error'     => false,
                    'message'   => $message,
                ]);
            }

        }catch(\Exception $e){
            $message = 'Cập nhật người dùng thất bại';
            return response()->json([
                'error'     => true,
                'message'   => $message,
            ]);
        }


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $name = $user->full_name;
        $user->delete();
        return response()->json([
            'name'=>$name
        ]);
    }

    public function profile()
    {

        return view('auth.info');
    }

    public function updateProfie(Request $request)
    {
        $input = $request->all();

        DB::beginTransaction();
        try {
            $user = User::find(Auth::user()->id);
            $success = $user->update($input);

            if($success)
            {
                DB::commit();
                $message = 'Cập nhật người dùng thành công';
                return response()->json([
                    'error'     => false,
                    'message'   => $message,
                ], 200);
            }
        } catch (\Exception $e) {
            DB::rollback();
            $message = 'Cập nhật người dùng thất bại';
            return response()->json([
                'error'     => true,
                'message'   => $message,
            ], 200);
        }
    }

    public function changePassword()
    {
        return view('auth.reset_password');
    }

    public function updatePassword(Request $request)
    {
        $user = User::find(Auth::user()->id);

        try{
            $validator = Validator::make($request->all(),
                [
                    'old_password' => 'required',
                    'password' => 'required|confirmed|min:6',
                ]
            );

            if(!$validator) return false;

            if(!Hash::check($request->get('old_password'), $user->password)) return false;

            $user->password = Hash::make($request->get('password'));



            $user->save();
            $request->session()->flash('message', 'cập nhật mật khẩu thành công!');
            return redirect()->route('calendar');

        }catch(\Exception $e){
            $request->session()->flash('message', 'cập nhật mật khẩu thất bại!');
            return redirect()->back();
        }
    }

    public function role($id)
    {
        $user = User::findOrFail($id);
        if ($user->is_active == 1)
            $user->is_active = 0;
        else
            $user->is_active = 1;
        $user->save();

        return response()->json([
            'user'=>$user->full_name
        ]);

    }

    public function checkEmailUnique(Request $request)
    {
        $statusCheck = true;

        $user = User::select('email')->where('email',$request->get('email'))->first();
        if(!empty($user)){
            $statusCheck = false;
        }
        return $statusCheck ? 'true':'false';
    }

    public function checkUserNameUnique(Request $request)
    {
        $statusCheck = true;

        $user = User::select('user_name')->where(['user_name'=>$request->get('user_name')])->first();
        if(!empty($user)){
            $statusCheck = false;
        }
        return $statusCheck ? 'true':'false';
    }

    public function checkEmailUniqueUpdate(Request $request)
    {
        $statusCheck = true;

        $user = User::select('email')->where('email',$request->get('email'))->where('id','<>',$request->get('id'))->first();
        if(!empty($user)){
            $statusCheck = false;
        }
        return $statusCheck ? 'true':'false';
    }

    public function checkUserNameUniqueUpdate(Request $request)
    {
        $statusCheck = true;

        $user = User::select('user_name')->where(['user_name'=>$request->get('user_name')])->where('id','<>',$request->get('id'))->first();
        if(!empty($user)){
            $statusCheck = false;
        }
        return $statusCheck ? 'true':'false';
    }

    public function checkOldPassword(Request $request){
        $status = true;

        $user = Auth::user();

        if(!Hash::check($request->get('old_password'), $user->password)) $status=false;

        return $status ? 'true':'false';

    }
}
