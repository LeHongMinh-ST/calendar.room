<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Schedule;
use App\Models\Semester;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class ScheduleController extends Controller
{
    public function index()
    {
        $today = date("Y-m-d");
        $rooms = Room::select('id', 'room_id')->get()->toArray();
        $now_semester = [];
        $semesters = Semester::select('id', 'semester', 'school_year','semester_start_date','semester_end_date')->orderBy('id','DESC')->limit(3)->get()->toArray();
        foreach ($semesters as $semester) {
            if (strtotime($today) >= strtotime($semester['semester_start_date']) && strtotime($today) <= strtotime($semester['semester_end_date'])) {
                $now_semester = $semester;
            }
        }
        return view('backend.schedules.index')->with([
            'rooms'=>$rooms,
            'semesters'=>$semesters,
            'now'=>$now_semester,
        ]);
    }

    public function getData(Request $request)
    {
//        dd($request->all());
        if($request->get('status')!=2){
            $schedules = Schedule::where('status',$request->get('status'));
        }else{
            $schedules  = Schedule::onlyTrashed();
        }


        if($request->get('room')!=null) {
            $schedules = $schedules->where('room_id',$request->get('room'));
        }

        $schedules = $schedules->orderBy('created_at','desc')->get();

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
            if($schedule->status != 2)
            {
                if($schedule->status ==0)
                    $string .= '<a href="javascript:void()" class="btn btn-primary btn-icon btn-edit" data-id="'.$schedule->id.'" title="xác nhận yêu cầu" style="margin-right: 5px"><i class="fas fa-check"></i></a>';
                $string .= '<a href="javascript:void()" class="btn btn-danger btn-icon btn-delete" data-id="'.$schedule->id.'" title="xóa yêu cầu"><i class="fas fa-trash-alt"></i></a>';
            }

            return $string;
        })
        ->addIndexColumn()
        ->rawColumns(['action','status'])
        ->make(true);
    }

    public function changeStatus($id)
    {
        try {
            $schedule = Schedule::findOrFail($id);
            $schedule->status = 1;
            $save = $schedule->save();

            if($save){
                $message = 'Xác nhận yêu cầu đăng kí thời khóa biểu thành công';
                return response()->json([
                    'erorr'=>false,
                    'message'   => $message,
                ]);
            }

        }catch (\Exception $e){
            $message = 'Xác nhận yêu cầu đăng kí thời khóa biểu thất bại';
            return response()->json([
                'error'     => true,
                'message'   => $message,
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            $schedule = Schedule::findOrFail($id);
            $schedule->status = 2;
            $schedule->save();
            $delete = $schedule->delete();

            if($delete){
                $message = 'Xóa yêu cầu đăng kí thời khóa biểu thành công';
                return response()->json([
                    'erorr'=>false,
                    'message'   => $message,
                ]);
            }

        }catch (\Exception $e){
            $message = 'Xóa yêu cầu đăng kí thời khóa biểu thất bại';
            return response()->json([
                'error'     => true,
                'message'   => $message,
            ]);
        }
    }

    public function show($id)
    {
        $schedule               = Schedule::find($id);
        $subjects               = Subject::select('id', 'name')->get()->toArray();
        $schedule->teacher_name = User::where('user_name', $schedule['teacher_id'])->first()->full_name;
        $schedule->subject      = $this->filterArray($subjects, $schedule['subject_id'])['name'];

        if($schedule)
        {
            return response()->json([
                'error'     => false,
                'schedule'  => $schedule,
            ]);
        }
    }

    public function filterArray($array, $id)
    {
        foreach ($array as $value) {
            if (isset($value['id'])) {
                if ($value['id'] == $id) {
                    return $value;
                }
            }
        }
        return false;
    }
}
