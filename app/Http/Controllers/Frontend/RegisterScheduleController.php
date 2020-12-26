<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Room;
use App\Models\User;
use App\Models\Subject;
use App\Models\Schedule;
use App\Models\Semester;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Week;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class RegisterScheduleController extends Controller
{
    public function index()
    {
        $today      = date("Y-m-d");
        $rooms      = Room::select('id', 'room_id','is_active')->where('is_active',1)->get()->toArray();
        $semesters  = Semester::select('id', 'semester', 'school_year','semester_start_date','semester_end_date')->orderBy('id','DESC')->limit(3)->get()->toArray();
        $subjects   = Subject::select('id', 'subject_id', 'name','is_active')->where('is_active',1)->get()->toArray();
        foreach ($semesters as $semester) {
            if (strtotime($today) >= strtotime($semester['semester_start_date']) && strtotime($today) <= strtotime($semester['semester_end_date'])) {
                $now_semester = $semester;
            }
        }

        return view('frontend.listRegister')->with([
            'rooms'         =>  $rooms,
            'semesters'     =>  $semesters,
            'now'           =>  $now_semester,
            'subjects'      =>  $subjects,
        ]);
    }

    public function show($id)
    {
        $schedule               = Schedule::withTrashed()->find($id);
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

    public function getData(Request $request)
    {
        if($request->get('status')!=2){
            $schedules  = Schedule::where('status',$request->get('status'));
        }else{
            $schedules  = Schedule::onlyTrashed();
        }


        if($request->get('room')!=null) {
            $schedules  = $schedules->where('room_id',$request->get('room'));
        }

        $schedules = $schedules->where('user_create_id',Auth::user()->id)->orderBy('created_at','desc')->get();


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
            $teacher = User::select('id','full_name')->where('id',$schedule->user_create_id)->first();
            return $teacher->full_name;
        })
        ->addColumn('user_create', function ($schedule) {
            $user_create = User::select('id','full_name')->find($schedule->user_create_id);
            return $user_create->full_name;
        })
        ->editColumn('status',function($schedule){
            $string = '';
            if($schedule->status == 1){
                $string = '<span class="badge badge-success">Đã xử lý</span>';
            }elseif($schedule->status == 0){
                $string = '<span class="badge badge-pill badge-warning">Chưa xử lý</span>';
            }else{
                $string = '<span class="badge badge-pill badge-danger">Đã hủy</span>';
            }

            return $string;
        })
        ->addColumn('checkbox',function ($schedule){
            return '<input type="checkbox" data-id="'.$schedule->id.'" class="dt-checkboxes" autocomplete="off">';
        })
        ->addColumn('action', function ($schedule) {
            $string = '';
            $string .= '<a href="javascript:void()" class="btn btn-success btn-icon btn-view" data-id="'.$schedule->id.'" title="chi tiết yêu cầu" style="margin-right: 5px"><i class="fas fa-eye"></i></a>';

            if ($schedule->status == 0)
                $string .= '<a href="javascript:void()" class="btn btn-primary btn-icon btn-edit" data-id="'.$schedule->id.'" title="sửa yêu cầu" style="margin-right: 5px"><i class="fas fa-edit"></i></a>';

            if(($schedule->status == 0 || Auth::user()->role_id == 1) && $schedule->status!=2)
                $string .= '<a href="javascript:void()" class="btn btn-danger btn-icon btn-delete" data-id="'.$schedule->id.'" title="xóa yêu cầu"><i class="fas fa-trash-alt"></i></a>';

            return $string;
        })
        ->addIndexColumn()
        ->rawColumns(['action','status','checkbox'])
        ->make(true);
    }

    public function edit($id)
    {
        $schedule = Schedule::find($id);

        if($schedule)
        {
            return response()->json([
                'error'     =>false,
                'schedule'  =>$schedule,
            ]);
        }

    }

    public function update(Request $request,$id)
    {

        try{

            $validator = Validator::make($request->all(),
                [
                    'edit_room'             =>'bail|required',
                    'edit_subject'          =>'bail|required',
                    'edit_group'            =>'bail|required|numeric',
                    'edit_class'            =>'bail|required',
                    'edit_quantity'         =>'bail|required|numeric',
                    'edit_weekDay'          =>'bail|required|numeric',
                    'edit_lesson_start'     =>'bail|required|numeric|min:1|max:13',
                    'edit_lesson_quantity'  =>'bail|required|numeric|min:1|max:5',
                    'edit_week_start'       =>'bail|required|numeric|min:1',
                    'edit_week_quantity'    =>'bail|required|numeric|min:1',
                    'edit_note'             =>'max:300',
                ],
            );

            if ($validator->fails()) {
                return false;
            }

             //kiểm tra số tiết
            if(!$this->checkLesson($request->get('edit_lesson_start'),$request->get('edit_lesson_quantity'))) return false;

            if(!$this->checkMaxWeekBackend($request->get('edit_week_start'))) return false;

            if(!$this->checkWeekSemesterBackend($request->get('week_start'), $request->get('edit_week_quantity'))) return false;

            if(!$this->checkDayStartBackend($request->get('edit_weekDay'), $request->get('edit_week_start'))) return false;

            if(!$this->checkUniqueSchedulesBackend($request->get('edit_room'), $request->get('edit_lesson_start'),$request->get('edit_weekDay'),$request->get('edit_week_start'))) return false;


            $schedule = Schedule::findOrFail($id);

            $schedule->room_id          = $request->get('edit_room');
            $schedule->subject_id       = $request->get('edit_subject');
            $schedule->subject_group    = $request->get('edit_group');
            $schedule->class            = $request->get('edit_class');
            $schedule->amount_people    = $request->get('edit_quantity');
            $schedule->day              = $request->get('edit_weekDay');
            $schedule->session          = $request->get('edit_lesson_start');
            $schedule->number_session   = $request->get('edit_lesson_quantity');
            $schedule->week             = $request->get('edit_week_start');
            $schedule->number_week      = $request->get('edit_week_quantity');
            $schedule->note             = $request->get('edit_note');
            $schedule->user_update_id   = Auth::user()->id;
            $save                       = $schedule->save();


            if($save){
                $message = 'Cập nhật thành công';
                return response()->json([
                    'error'     =>false,
                    'message'   =>$message,
                ]);
            }


        }catch(\Exception $e)
        {
            $message = 'Cập nhật thất bại';
            return response()->json([
                'error'     =>true,
                'message'   =>$message,
            ]);
        }
    }

    public function checkUniqueUpdateSchedules(Request $request,$id)
    {
        $status = true;

        $schedule = Schedule::where([
            'semester_id'   =>  Session::get('semester_now')['id'],
            'room_id'       =>  $request->get('room'),
            'session'       =>  $request->get('lesson_start'),
            'status'        =>  '1',
            'day'           =>  $request->get('weekDay'),
            'week'          =>  $request->get('week_start'),
        ])
        ->where('id', '<>', $id)
        ->first();

        if(!empty($schedule)){
            $status = false;
        }

        return response()->json([
            'status' => $status,
        ]);
    }

    public function checkUniqueSchedulesBackend($room,$lessonStart,$weekDay,$weekStart)
    {
        $status = true;

        $schedule = Schedule::where([
            'semester_id'   =>  Session::get('semester_now')['id'],
            'room_id'       =>  $room,
            'session'       =>  $lessonStart,
            'status'        =>  '1',
            'day'           =>  $weekDay,
            'week'          =>  $weekStart,
        ])->first();

        if(!empty($schedule)){
            $status = false;
        }

        return $status;
    }

    public function checkWeekSemesterBackend($week_start,$week_quantity)
    {
        $status     = true;

        $weekNumber = $week_start + $week_quantity-1;

        if($weekNumber > Session::get('semester_now')['number_weeks']){
            $status = false;
        }

        return $status;

    }

    public function checkDayStartBackend($weekDay,$week_start)
    {
        $status = true;

        $day    = $weekDay -2;

        $weekdate = Week::select('id', 'week', 'start_day')
            ->where('semester_id', Session::get('semester_now')['id'])
            ->where('week', $week_start)
            ->first()->toArray();

        $dateStart  = date('Y-m-d', strtotime($weekdate['start_day'] . ' + ' . $day . ' days'));
        $dateNow    = date('Y-m-d');

        if(strtotime($dateStart)<strtotime($dateNow))
        {
            $status = false;
        }

        return $status;
    }

    public function checkMaxWeekBackend($weekStart)
    {
        $status = true;

        if($weekStart>Session::get('semester_now')['number_weeks'])
        {
            $status = false;
        }

        return $status;
    }

    public function checkLesson($lessonStart,$lesson)
    {
        if($lessonStart <=5)
            return ($lesson <= 5-$lessonStart+1) ? true : false;
        else if($lessonStart >5 && $lessonStart <=10)
            return ($lesson <=11-$lessonStart) ? true : false;
        else
            return $lesson <=14-$lessonStart ? true : false;
    }

    public function deleteSelected(Request $request){
        try {
            $data = $request->id;

            if($data == null)
                return false;

            $success = Schedule::whereIn('id',$data)->delete();
            if($success){
                return response()->json([
                    'error'     => false,
                    'message'   => "Xóa thành công các yêu cầu đã chọn"
                ]);
            }

        }catch (\Exception $e){
            return response()->json([
                'error'     => true,
                'message'   => $e->getMessage()
            ]);
        }

    }
}
