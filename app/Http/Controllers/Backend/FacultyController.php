<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class FacultyController extends Controller
{

    public function getData()
    {
        $faculty = Faculty::all();
        return DataTables::of($faculty)
            ->editColumn('is_active', function ($faculty){
                $string ='';
                if($faculty->is_active == 1){
                    $string .=   '<form action="" method="post">
                                        <label class="switch">
                                            <input class="active_switch" type="checkbox" id="active_switch' . $faculty->id . '" checked="checked" data-id="' . $faculty->id . '">
                                            <span class="slider round"></span>
                                        </label>
                                    </form>';
                }else{
                    $string .=   '<form action="" method="post">
                                        <label class="switch">
                                            <input class="active_switch" type="checkbox" id="active_switch' . $faculty->id . '" data-id="' . $faculty->id . '">
                                            <span class="slider round"></span>
                                        </label>
                                    </form>';
                }
                return $string;
            })
            ->addColumn('action', function ($faculty) {
                $string ='';
                $string .=
                    '
                    <a href="' . $faculty->id . '" data-id="' . $faculty->id . '" class="btn btn-primary  btn-edit" title="sửa thông tin khoa"><i class="fas fa-edit"></i></a>
                    <a href="" data-id="' . $faculty->id . '" class="btn btn-danger btn-delete" title="xóa khoa" ><i class="fas fa-trash-alt"></i></a>';
                return $string;
            })
            ->addIndexColumn()
            ->rawColumns(['is_active', 'action'])
            ->make(true);
    }

    public function status($id)
    {
        $faculty = Faculty::findOrFail($id);
        if ($faculty->is_active == 1)
            $faculty->is_active = 0;
        else
            $faculty->is_active = 1;
        $faculty->save();

        return response()->json([
            'room'=>$faculty->faculty_id
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('backend.faculties.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

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
            $validator = Validator::make($request->all(),
                [
                    'add_faculty_id'=>'required',
                    'add_faculty_name'=>'required',
                ]
            );

            if($validator->fails()) {
                return false;
            }

            //Tạo mới
            $faculty = new Faculty();
            $faculty->faculty_id = strtoupper($request->add_faculty_id);
            $faculty->name = $request->add_faculty_name;
            $faculty->user_create_id = Auth::user()->id;
            $faculty->user_update_id = Auth::user()->id;
            $success = $faculty->save();
            if ($success) {
                $message = 'Thêm mới thành công khoa '.$faculty->faculty_id;
                return response()->json([
                    'error'     => false,
                    'message'   => $message,
                ]);
            }
        }catch (\Exception $e){
            $message = 'Thêm mới khoa thất bại';
            return response()->json([
                'error' => true,
                'message' => $message,
            ]);
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
        $faculty=Faculty::findOrFail($id);
        return response()->json([
            'faculty'=>$faculty,
            'error' =>  false,
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
            $validator = Validator::make($request->all(),
                [
                    'edit_faculty_id'       =>'required',
                    'edit_faculty_name'     =>'required',
                ]
            );
            if ($validator->fails()) {
                return false;
            }

            $faculty = Faculty::find($id);
            $faculty->faculty_id = strtoupper($request->edit_faculty_id);
            $faculty->name = $request->edit_faculty_name;
            $faculty->user_update_id = Auth::user()->id;
            $success = $faculty->save();
            if ($success) {
                $message = 'Sửa thành công khoa '.$faculty->faculty_id;
                return response()->json([
                    'error'     => false,
                    'message'   => $message,
                ]);
            }
        }catch (\Exception $e){
            $message = 'Sửa thông tin khoa thất bại';
            return response()->json([
                'error' => true,
                'message' => $message,
            ]);
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
        $faculty = Faculty::findOrFail($id);
        $faculty->delete();
        //đợi để sửa cấu trúc bảng department
//        $facultyInDepartment =  Department::select('room_id')->where('room_id','=',$id)->get();
//        if(count($facultyInDepartment)>0) {
//            $statusCheck  = false;
//        }else{
//            $faculty->delete();
//        }
        return response()->json([
            'statusCheck'   =>$statusCheck,
            'faculty_id'    =>$faculty->faculty_id,
        ]);
    }

    public function checkUniqueFacultyID(Request $request){
        $statusCheck  = true;
        $id = (int)$request->id;

        if($id == null){
            $faculty = Faculty::select('id','faculty_id','name')
                ->where('faculty_id',$request->faculty_id)->get();
        }else{
            $faculty = Faculty::select('id','faculty_id','name')
                ->where('faculty_id',$request->faculty_id)->where('id','<>',$id)->get();
        }
        if(count($faculty)>0) {
            $statusCheck  = false;
        }
        return $statusCheck;
    }

    public function checkUniqueFacultyName(Request $request){
        $statusCheck  = true;
        $id = (int)$request->id;

        if($id == null){
            $faculty = Faculty::select('id','faculty_id','name')
                ->where('name',$request->name)->get();
        }else{
            $faculty = Faculty::select('id','faculty_id','name')
                ->where('name',$request->name)->where('id','<>',$id)->get();
        }
        if(count($faculty)>0) {
            $statusCheck  = false;
        }
        return $statusCheck;
    }
}
