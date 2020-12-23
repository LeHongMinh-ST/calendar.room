<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Schedule;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Psy\Util\Str;
use Validator;
use Yajra\DataTables\DataTables;

class SubjectController extends Controller
{
    public function getData()
    {
        return DataTables::of(Subject::query())
            ->addColumn('department', function ($subject) {
                $department=Department::find($subject->department_id);
                if(!empty($department)){
                    return $department->name;
                }
                return "Đang cập nhật";
            })->addColumn('user_create', function ($subject) {
                $user=User::find($subject->user_create_id);
                if(!empty($user)){
                    return $user->full_name;
                }
                return "Đang cập nhật";
            })->editColumn('is_active', function ($subject) {
                $string ='';
                if($subject->is_active == 1){
                    $string .=   '<form action="" method="post">
                                    <label class="switch">
                                        <input class="active_switch" type="checkbox" id="active_switch' . $subject->id . '" checked="checked" data-id="' . $subject->id . '">
                                        <span class="slider round"></span>
                                    </label>
                                </form>';
                }else{
                    $string .=   '<form action="" method="post">
                                    <label class="switch">
                                        <input class="active_switch" type="checkbox" id="active_switch' . $subject->id . '" data-id="' . $subject->id . '">
                                        <span class="slider round"></span>
                                    </label>
                                </form>';
                }

                return $string;
            })->addColumn('action', function ($subject) {
                $s='<a data-id="' . $subject->id . '" class="btn btn-primary btn-icon btn-edit" title="sửa môn học"><i class="fas fa-edit"></i></a>';
               $semester =Session::get('semester_now');
//                        <a href="" data-id="' . $subject->id . '" class="btn btn-danger btn-delete" title="xóa môn học"><i class="fas fa-trash-alt"></i></a>
                return $s;
            })->addIndexColumn()
            ->rawColumns(['is_active', 'action'])
            ->make(true);
    }


    public function index()
    {
        $departments = Department::select('id','department_id','name','is_active')->where('is_active',1)->get()->toArray();
        return view('backend.subjects.index')->with([
            'departments'=>$departments
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $departments = Department::select('id','department_id','name')->where('is_active',1)->get()->toArray();
        return view('backend.subjects.create')->with([
           'departments'=>$departments
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validate= Validator::make($request->all(),[
            'subject_id' => 'required|unique:subjects|min:4|max:15',
            'name' => 'required|min:3|max:100',
            'department_id'=> 'required'

        ]);
        if ($validate->fails()) {
            return false;
        }

        $datas=$request->all();

        DB::beginTransaction();
        try {
            $datas['user_create_id']=Auth::user()->id;
            $datas['user_update_id']=Auth::user()->id;
            $datas['subject_id'] = strtoupper($datas['subject_id']);
            $subject=Subject::where('subject_id',$datas['subject_id'])->first();
            if($subject !=null){
                return response()->json([
                    'error'   => true,
                    'message' => 'Mã môn học đã tồn tại!'
                ]);
            }
            $subject=Subject::create($datas);
            DB::commit();
            return response()->json([
                'error'   => false,
                'message' => 'Thêm mới thành công môn học!'
            ]);

        } catch (Exception $e) {
            DB::rollback();
            response()->json([
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

        $subject=Subject::findOrFail($id);
        $departments = Department::select('id','department_id','name')->where('is_active',1)->get()->toArray();

        return response()->json([
            'subject'=>$subject,
            'departments'=>$departments
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
        $validator= Validator::make($request->all(),[
            'subject_id' => 'required|min:4|max:15',
            'name' => 'required|min:3|max:100',
            'department_id'=> 'required'
        ]);
        if ($validator->fails()) {
            return false;
        }
        $subject=Subject::findOrFail($id);
        $datas=$request->all();
        DB::beginTransaction();
        try {
            if($subject->subject_id != $datas['subject_id']){
                $temp=Subject::where('subject_id',$datas['subject_id'])->first();
                if($temp!=null){
                    return false;
                }
            }
            $datas['user_update_id']=Auth::user()->id;
            $datas['subject_id'] = strtoupper($request->get('subject_id'));
            $subject->update($datas);
            DB::commit();
            return response()->json([
                'error'   => false,
                'message' => 'Cập nhật thành công môn học!',
                'data'=> $datas
            ]);
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
        $subject=Subject::findOrFail($id);
        $subject->delete();
        return response()->json([
            'name'=>$subject->name
        ]);

    }

    public function checkSubjectIdUnique(Request $request)
    {
        $statusCheck = true;

        $subject = Subject::select('subject_id')->where(['subject_id'=>$request->get('subject_id')])->first();
        if(!empty($subject)){
            $statusCheck = false;
        }
        return $statusCheck ? 'true':'false';
    }

    public function checkSubjectIdUniqueUpdate(Request $request)
    {
        $statusCheck = true;

        $subject = Subject::select('subject_id')->where(['subject_id'=>$request->get('subject_id')])->where('id','<>',$request->get('id'))->first();
//        dd($subject);
        if(!empty($subject)){
            $statusCheck = false;
        }
        return $statusCheck ? 'true':'false';
    }

    public function toggleActive($id)
    {
        $subject = Subject::findOrFail($id);
        if ($subject->is_active == 1)
            $subject->is_active = 0;
        else
            $subject->is_active = 1;
        $subject->save();

        return response()->json([
            'subject'=>$subject->name
        ]);

    }



}
