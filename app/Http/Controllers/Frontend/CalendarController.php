<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Imports\ScheduleImport;
use App\Models\Room;
use App\Models\Schedule;
use App\Models\Semester;
use App\Models\Subject;
use App\Models\Week;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class CalendarController extends Controller
{
    public function __construct()
    {

    }

    public function getView()
    {
        if (!Session::has('semesters')) {
            $today      = date("Y-m-d");
            $semesters  = Semester::select('id', 'semester', 'school_year','number_weeks', 'semester_start_date', 'semester_end_date')->get()->toArray();
            foreach ($semesters as $semester) {
                if (strtotime($today) >= strtotime($semester['semester_start_date']) && strtotime($today) <= strtotime($semester['semester_end_date'])) {
                    Session::put('semesters', $semester);
                    Session::put('semester_now',$semester);
                }
            }
        }

        if (!Session::has('room')) {
            $room = Room::select('id','room_id')->where('room_id','THCNTT01')->first();
            if(!empty($room)) {
                $room = $room->toArray();
                Session::put('room', $room);
            }
        }

        //Xử lý các thời khóa biểu đã hết hạn xác nhận

        $schedules = Schedule::where('status',0)->get();
        // dd($schedules);
        foreach($schedules as $schedule)
        {
            $day    = $schedule->day -2;

            $weekdate = Week::select('id', 'week', 'start_day')
                ->where('semester_id', Session::get('semester_now')['id'])
                ->where('week', $schedule->week)
                ->first();

            $dateStart  = date('Y-m-d', strtotime($weekdate['start_day'] . ' + ' . $day . ' days'));
            $dateNow    = date('Y-m-d');

            if(strtotime($dateStart)<strtotime($dateNow))
            {
                $schedule->status = 2;
                $schedule->save();
                $schedule->delete();
            }
        }

    }

    public function index()
    {
        $this->getView();
        $rooms      = Room::select('id', 'room_id', 'is_active')->where('is_active',1)->get()->toArray();
        $subjects   = Subject::select('id', 'subject_id', 'name')->where('is_active',1)->get()->toArray();
        $semesters  = Semester::select('id', 'semester','number_weeks', 'school_year')->orderBy('id','DESC')->limit(3)->get()->toArray();

        return view('frontend.calendar')->with([
            'rooms' => $rooms,
            'subjects' => $subjects,
            'semesters' => $semesters,
        ]);
    }


    //Lấy các sự kiện trong một học kì
    public function getSchedules()
    {
        //Lấy phòng học và học kì
        $this->getView();

        $semester_id    = Session::get('semesters')['id'];//id của học kì

        //lấy ra id phòng học hiện tại
        $room_id        = Room::select('id', 'room_id','is_active')->where(['id'=>Session::get('room')['id'],'is_active'=>1])->first();

        //Lấy ra các môn học
        $subjects       = Subject::select('id', 'name')->get()->toArray();

        //Lấy ra thời khóa biểu theo phòng và học kì
        $schedules = Schedule::select('id', 'room_id', 'semester_id', 'subject_id', 'subject_group', 'teacher_id',
            'class', 'amount_people', 'day', 'session', 'number_session', 'week', 'number_week', 'status', 'week_check')
            ->where('semester_id', $semester_id)
            ->where('room_id', $room_id->id)
            ->get()->toArray();

        //Tạo mảng event
        $events = [];


        //Tạo event cho học kì
        foreach ($schedules as $schedule) {
            $arrayWeeks = explode(",",$schedule['week_check']);
            $arrayWeeks = $this->getChildArray($arrayWeeks);
            $teacher = User::select('id','user_name','full_name')->where(['user_name'=> $schedule['teacher_id']])->first();

            foreach ($arrayWeeks as $arrayWeek){
                //Lấy các thông tin cho lớp học
                $event['group']             = $schedule['subject_group'];
                $event['class']             = $schedule['class'];
                $event['amount_people']     = $schedule['amount_people'];
                $event['teacher_id']        = $schedule['teacher_id'];
                $event['teacher_name']      = $teacher ? $teacher['full_name'] : 'Chưa rõ';
                $event['subject']           = $this->filterArray($subjects, $schedule['subject_id'])['name'];
                //Lấy các thông tin cho event
                $event['title']             = $event['subject'] . "
                Giảng viên: " . $event['teacher_name'] . "
                Lớp : " . $event['class'];
                $datetime                   = $this->timeEvent($schedule,$arrayWeek);
                $event['startTime']         = $datetime['time_start'];
                $event['endTime']           = $datetime['time_end'];
                $event['startRecur']        = $datetime['date_start'];
                $event['endRecur']          = $datetime['date_end'];
                $event['daysOfWeek']        = [$schedule['day'] - 1];
                $event['color']             = ($schedule['status'] == 1) ? '#007bff' : '#ffc107';
                $event['schedule']          = $schedule;
                $events[]                   = $event;
            }

        }

        return Response::json($events);

    }

    public function changeSchedules(Request $request)
    {

        $semester   = Semester::select('id', 'semester', 'school_year', 'semester_start_date', 'semester_end_date')->where(['id'=>$request->get('filterSemester')])->first();
        if($semester) Session::put('semesters', $semester);


        $room       = Room::select('id','room_id','is_active')->where(['id'=>$request->get('filterRoom'),'is_active'=>1])->first();
        if($room) Session::put('room', $room);

        return redirect()->route('calendar');
    }

    public function importExcel(Request $request){
        $input = $request->allFiles();

        try {
            $status = Excel::import(new ScheduleImport(),$input['excel']);

            if($status){
                $message = 'Nhập dữ liệu thành công';
                return response()->json([
                    'error'     => false,
                    'message'   => $message
                ]);
            }
        }catch (\Exception $e){
            $mesage = 'Tạo yêu cầu thất bại!';
            return response()->json([
                'error'     => true,
                'message'   => $mesage
            ]);
        }
    }

    public function create()
    {
        $this->getView();
        $rooms      = Room::select('id', 'room_id','is_active')->where('is_active',1)->get()->toArray();
        $subjects   = Subject::select('id', 'subject_id', 'name','is_active')->where('is_active',1)->get()->toArray();
        $semester   = Session::get('semester_now');
        $weeks      = Week::select('id', 'week', 'start_day','end_day')
            ->where('semester_id', $semester['id'])->get();


        $weekNow = null;

        foreach($weeks as $week)
        {
            if(strtotime($week->start_day) <= strtotime(date("Y/m/d")) && strtotime($week->end_day)>=strtotime(date("Y/m/d")))
            {
                $weekNow =  $week->week;
            }
        }


        return view('frontend.registerCalendar')->with([
            'rooms'     => $rooms,
            'subjects'  => $subjects,
            'weekNow'   => $weekNow,
        ]);
    }

    public function store(Request $request)
    {
        try {

            $validator = Validator::make($request->all(),
                [
                    'room'              =>'bail|required',
                    'subject'           =>'bail|required',
                    'group'             =>'bail|required|numeric',
                    'class'             =>'bail|required',
                    'quantity'          =>'bail|required|numeric',
                    'weekDay'           =>'bail|required|numeric',
                    'lesson_start'      =>'bail|required|numeric|min:1|max:13',
                    'lesson_quantity'   =>'bail|required|numeric|min:1|max:5',
                    'week_start'        =>'bail|required|numeric|min:1',
                    'week_quantity'     =>'bail|required|numeric|min:1',
                    'note'              =>'bail|max:300'
                ]
            );


            if ($validator->fails()) {
                return false;
            }

            //Kiểm tra số tiết
            if(!$this->checkLesson($request->get('lesson_start'),$request->get('lesson_quantity'))) return false;

            if(!$this->checkMaxWeekBackend($request->get('week_start'))) return false;

            if(!$this->checkWeekSemesterBackend($request->get('week_start'), $request->get('week_quantity'))) return false;

            if(!$this->checkDayStartBackend($request->get('weekDay'), $request->get('week_start'))) return false;

            if(!$this->checkUniqueSchedulesBackend($request->get('room'), $request->get('lesson_start'),$request->get('weekDay'),$request->get('week_start'))) return false;

            $schedule                   = new Schedule();
            $schedule->room_id          = $request->get('room');
            $schedule->semester_id      = Session::get('semesters')['id'];
            $schedule->subject_id       = $request->get('subject');
            $schedule->subject_group    = $request->get('group');
            $schedule->teacher_id       = Auth::user()->user_name;
            $schedule->class            = $request->get('class');
            $schedule->amount_people    = $request->get('quantity');
            $schedule->day              = $request->get('weekDay');
            $schedule->session          = $request->get('lesson_start');
            $schedule->number_session   = $request->get('lesson_quantity');
            $schedule->week             = $request->get('week_start');
            $schedule->number_week      = $request->get('week_quantity');
            $schedule->note             = $request->get('note');
            $schedule->week_check       = implode(',',$request->get('weekCheck'));
            $schedule->user_create_id   = Auth::user()->id;
            $schedule->user_update_id   = Auth::user()->id;
            $success                    = $schedule->save();

            if($success)
            {
                $mesages = 'Tạo yêu cầu thành công! Chờ quản trị viên xét duyệt !';

                return redirect()->route('calendar')->with([
                    'error'     => false,
                    'message'   => $mesages
                ]);
            }

        } catch (\Exception $e) {
            $mesages = 'Tạo yêu cầu thất bại!';
            return redirect()->back()->with([
                'error'     => true,
                'message'   => $mesages
            ]);
        }

    }

    public function edit($id)
    {
        $schedule = Schedule::findOrFail($id);

        return response()->json([
            'schedule'=>$schedule,
        ]);
    }

    public function update(Request $request, $id)
    {
        try {
            // dd($request->all());
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
                ]
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
            $schedule->week_check       = implode(',',$request->get('weekCheck'));
            $schedule->user_update_id   = Auth::user()->id;
            $success                    = $schedule->save();

            if($success)
            {
                $mesage = 'Cập nhật yêu cầu thành công! Chờ quản trị viên xét duyệt !';

                return response()->json([
                    'error'     => false,
                    'message'   => $mesage
                ]);
            }

        } catch (\Exception $e) {
            $mesage = 'Cập nhật yêu cầu thất bại!';

            return response()->json([
                'error'=>true,
                'message'=>$e->getMessage()
            ]);
        }
    }

    //Hàm tìm kiếm trong mảng
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

    public function timeEvent($schedule,$dataWeek)
    {
        //Lấy ra tuần bắt đầu

        $weekStart = Week::select('id', 'week', 'start_day')
            ->where('semester_id', $schedule['semester_id'])
            ->where('week', reset($dataWeek))
            ->first()->toArray();
        end($dataWeek);
        $weekEnd = Week::select('id', 'week', 'end_day')
            ->where('semester_id', $schedule['semester_id'])
            ->where('week', current($dataWeek))
        ->first()->toArray();

        //Lấy ra ngày bắt đầu môn học
        $day = $schedule['day'] - 2;
        $datetime['date_start']     = date('Y-m-d', strtotime($weekStart['start_day'] . ' + ' . $day . ' days'));
        $datetime['date_end']     = date('Y-m-d', strtotime($weekEnd['end_day']));

        //Lấy ra thời gian bắt đầu và thời gian kết thúc
        $datetime['time_start']     = config('settime.' . $schedule['session'])['start'];
        $datetime['time_end']       = config('settime.' . ($schedule['session'] + $schedule['number_session'] - 1))['end'];

        return $datetime;
    }

    public function checkUniqueSchedules(Request $request)
    {
        $status = true;

        $schedule = Schedule::where([
            'semester_id'   =>  Session::get('semester_now')['id'],
            'room_id'       =>  $request->get('room'),
            'session'       =>  $request->get('lesson_start'),
            'status'        =>  '1',
            'day'           =>  $request->get('weekDay'),
            'week'          =>  $request->get('week_start'),
        ])->first();

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

    public function checkWeekSemester(Request $request)
    {
        $status     = true;

        $weekNumber = $request->get('week_start')+$request->get('week_quantity')-1;

        if($weekNumber > Session::get('semester_now')['number_weeks']){
            $status = false;
        }

        return $status ? 'true' : 'false';

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

    public function checkDayStart(Request $request)
    {
        $status = true;

        $day    = $request->get('weekDay') -2;

        $weekdate = Week::select('id', 'week', 'start_day')
            ->where('semester_id', Session::get('semester_now')['id'])
            ->where('week', $request->get('week_start'))
            ->first()->toArray();

        $dateStart  = date('Y-m-d', strtotime($weekdate['start_day'] . ' + ' . $day . ' days'));
        $dateNow    = date('Y-m-d');

        if(strtotime($dateStart)<strtotime($dateNow))
        {
            $status = false;
        }

        return $status ? 'true':'false';
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

    public function getWeekNow(Request $request)
    {
        $semester   = Session::get('semester_now');
        $weeks      = Week::select('id', 'week', 'start_day','end_day')
                    ->where('semester_id', $semester['id'])->get();

        foreach($weeks as $week)
        {
            if(strtotime($week->start_day) <= strtotime($request->get('date')) && strtotime($week->end_day)>=strtotime($request->get('date')))
            {
                return response()->json([
                    'error'=> false,
                    'week'=>$week->week
                ]);
            }
        }

        return response()->json([
            'error'=> true,
        ]);
    }

    public function checkMaxWeek(Request $request)
    {
        $status = true;

        if($request->get('week_start')>Session::get('semester_now')['number_weeks'])
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

    public function getChildArray(array $data){
        $stack = [];
        $start =0;

        for($i=0;$i<count($data)-1;$i++){
            if($data[$i + 1] - $data[$i] > 1){
                $stack[] = array_slice($data,$start,$i-$start+1);
                $start = $i+1;
            }
        }

        $stack[] = array_slice($data,$start);
        return $stack;
    }

}
