<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Room;
use App\Models\Schedule;
use App\Models\Subject;
use App\Models\User;
class DashboardController extends Controller

{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = User::all()->count();
        $room = Room::all()->count();
        $schedule = Schedule::where('status',0)->count();
        $feedback = Feedback::all()->count();
        return view('backend.dashboard')->with([
            'user'=>$user,
            'room'=>$room,
            'schedule'=>$schedule,
            'feedback'=>$feedback
        ]);
    }

    public function getData()
    {

        $schedules = $schedules = Schedule::where('status',0)->orderBy('created_at','desc')->get();

        return DataTables::of($schedules)
        ->editColumn('room_id',function($schedule){
            $room = Room::select('id','room_id')->where('id',$schedule->room_id)->first();
            return $room->room_id;
         })
        ->editColumn('subject_id',function($schedule){
            $subject = Subject::select('id','subject_id')->where('id',$schedule->subject_id)->first();
            return $subject->subject_id;
        })
        ->editColumn('subject_name',function($schedule){
            $subject = Subject::select('id','subject_id','name')->where('id',$schedule->subject_id)->first();
            return $subject->name;
        })->editColumn('created_at',function($schedule){
            $date = $schedule->created_at;
            return $date;
        })
        ->addColumn('teacher_name',function($schedule){
            $teacher = User::select('id','full_name','user_name')->where('user_name',$schedule->teacher_id)->first();
            return $teacher->full_name;
        })
        ->addColumn('user_create', function ($schedule) {
            $user_create = User::select('id','full_name')->find($schedule->user_create_id);
            return $user_create->full_name;
        })
        ->editColumn('status',function($schedule){
            if($schedule->status == 1){
                return '<span class="badge badge-success">Đã xử lý</span>';
            }elseif($schedule->status == 0){
                return '<span class="badge badge-pill badge-warning">Chưa xử lý</span>';
            }else{
                return '<span class="badge badge-pill badge-danger">Đã hủy</span>';
            }
        })
        ->addColumn('action', function ($schedule) {
            $string = '';
            $string .= '<a href="javascript:void()" class="btn btn-success btn-icon btn-view" data-id="'.$schedule->id.'" title="chi tiết yêu cầu" style="margin-right: 5px"><i class="fas fa-eye"></i></a>';
            $string .= '<a href="javascript:void()" class="btn btn-primary btn-icon btn-edit" data-id="'.$schedule->id.'" title="xác nhận yêu cầu" style="margin-right: 5px"><i class="fas fa-check"></i></a>';
            return $string;
        })
        ->addIndexColumn()
        ->rawColumns(['action','status'])
        ->make(true);
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
