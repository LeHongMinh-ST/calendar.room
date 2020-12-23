<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Validator;
use Yajra\DataTables\DataTables;

class DepartmentController extends Controller
{
    public function getData(Request $request)
    {
        if($request->get('faculty_id')!=null){
            $departments = Department::where('faculty_id','=',$request->get('faculty_id'))->orderBy('faculty_id','ASC');
        }else{
            $departments = Department::select('id', 'faculty_id', 'department_id', 'name', 'is_active')->orderBy('faculty_id','ASC');
        }
        $departments = $departments->get();
        $user_login = Auth::user();
        return DataTables::of($departments)
            ->editColumn('faculty_id', function ($departments) {
                $faculty=Faculty::find($departments->faculty_id)->name;
                return $faculty;
            })
            ->editColumn('is_active', function ($department) use ($user_login) {
                $string ='';
                if($department->is_active == 1){
                    $string .=   '<form action="" method="post">
                                        <label class="switch">
                                            <input class="active_switch" type="checkbox" id="active_switch' . $department->id . '" checked="checked" data-id="' . $department->id . '">
                                            <span class="slider round"></span>
                                        </label>
                                   </form>';
                }else{
                    $string .=   '<form action="" method="post">
                                        <label class="switch">
                                            <input class="active_switch" type="checkbox" id="active_switch' . $department->id . '" data-id="' . $department->id . '">
                                            <span class="slider round"></span>
                                        </label>
                                   </form>';
                }
                return $string;
            })
            ->addColumn('action', function ($department) {
                return '<a data-id="' . $department->id . '" class="btn btn-primary btn-icon btn-edit" title="sửa bộ môn"><i class="fas fa-edit"></i></a>
                        <a href="" class="btn btn-danger btn-delete" data-id="' . $department->id . '" title="xóa lịch trực" ><i class="fas fa-trash-alt"></i></a>';
            })
            ->addIndexColumn()
            ->rawColumns(['is_active', 'action'])
            ->make(true);
    }


    public function index()
    {
        $faculties = Faculty::select('id','name')->where('is_active',1)->get()->toArray();
        $fillterFaculty = Faculty::select('id', 'name')->get()->toArray();
        return view('backend.departments.index',["faculties"=>$faculties, "fillterFaculty"=>$fillterFaculty]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.departments.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validate= Validator::make($request->all(),[
                'department_id' => 'required|min:2|max:15',
                'name' => 'required|min:3|max:100',
                'faculty_id' => 'required'
            ]);

            if ($validate->fails()) {
                return false;
            }

            $checkIdDepartment = Department::select('department_id')->where(['department_id'=>$request->get('department_id')])->first();
            if(!empty($checkIdDepartment)){
                return false;
            }

            $checkNameDepartment = Department::select('name')->where('name',$request->get('name'))->first();
            if(!empty($checkNameDepartment)){
                return false;
            }

            $department = new Department();
            $department->faculty_id = $request->faculty_id;
            $department->department_id = strtoupper($request->department_id);
            $department->name = $request->name;
            $department->user_create_id = Auth::user()->id;
            $department->user_update_id = Auth::user()->id;
            $success = $department->save();

            if($success){
                return response()->json([
                    'error'   => false,
                    'message' => 'Thêm mới thành công bộ môn '.$department->name
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'error'   => true,
                'message' => $e
            ], 500);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $department=Department::findOrFail($id);
        $faculty = Faculty::select('name','id')->where('id',$department->faculty_id)->first();
        $department->faculty_id = $faculty->id;
        return response()->json([
            'department'=>$department,
        ]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $validator= Validator::make($request->all(),[
                'department_id' => 'required|min:2|max:15',
                'name' => 'required|min:3|max:100',
            ]);
            if ($validator->fails()) {
                return false;
            }

            $departments = Department::where('department_id',$request->department_id)
                ->where('faculty_id',$request->faculty_id)->where('id','<>',$id)->get();
            if(count($departments)>0){
                return false;
            }

            $department = Department::find($id);
            $department->faculty_id = $request->faculty_id;
            $department->department_id = strtoupper($request->department_id);
            $department->name = $request->name;
            $department->user_update_id = Auth::user()->id;
            $success = $department->save();

            if($success){
                return response()->json([
                    'error'   => false,
                    'message' => 'Cập nhật thành công bộ môn '.$department->department_id
                ]);
            }
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'error'   => true,
                'message' => $e
            ], 500);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $statusCheck = true;
        $department = Department::findOrFail($id);
        $checkDepartmentIDInSubject = Subject::select('id','department_id','name' )->where('department_id','=',$id)->get();
        if(count($checkDepartmentIDInSubject)>0) {
            $statusCheck = false;
            $message = 'Bộ môn này đã được sử dụng, không thể xóa bộ môn này';
        }else{
            $message = 'Xóa bộ môn thành công';
            $department->delete();
        }
        $statusCheck ? 'true': 'false';
        return response()->json([
            'checkAssignment'        =>$statusCheck,
            'message'                =>$message,
        ]);
    }
    public function toogleActive($id)
    {
        $department = Department::findOrFail($id);
        if ($department->is_active == 1)
            $department->is_active = 0;
        else
            $department->is_active = 1;
        $department->save();

        return response()->json([
            'department'=>$department->name
        ]);

    }
    public function checkDepartmentIdUnique(Request $request)
    {
        $statusCheck = true;

        $department = Department::select('department_id')->where(['department_id'=>$request->get('department_id')])->first();
        if(!empty($department)){
            $statusCheck = false;
        }
        return $statusCheck ? 'true':'false';
    }

    public function checkDepartmentIdUniqueUpdate(Request $request)
    {
        $statusCheck = true;

        $department = Department::select('id','department_id')->where(['department_id'=>$request->get('department_id')])->where('id','<>',$request->get('id'))->first();
        if(!empty($department)){
            $statusCheck = false;
        }
        return $statusCheck ? 'true':'false';
    }

    public function checkNameUnique(Request $request)
    {
        $statusCheck = true;

        $department = Department::select('id','name')->where(['name'=>$request->get('name')])->first();
        if(!empty($department)){
            $statusCheck = false;
        }
        return $statusCheck ? 'true':'false';
    }

    public function checkNameUniqueUpdate(Request $request)
    {
        $statusCheck = true;

        $department = Department::select('id','name')->where(['name'=>$request->get('name')])->where('id','<>',$request->get('id'))->first();
        if(!empty($department)){
            $statusCheck = false;
        }
        return $statusCheck ? 'true':'false';
    }

    public function checkIsActiveFaculty(Request $request){
        $statusCheck = true;

        $faculty = Faculty::findOrFail($request->faculty_id);
        $department = Department::findOrFail($request->department_id);
        if($faculty->is_active == 0 && $department->faculty_id != $faculty->id){
            $statusCheck = false;
        }
        return $statusCheck ? 'true':'false';
    }


}
