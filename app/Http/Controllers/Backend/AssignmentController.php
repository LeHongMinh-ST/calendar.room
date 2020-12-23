<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Room;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class AssignmentController extends Controller
{
    public function getData(Request $request)
    {
        if($request->get('semester_id')!=null){
            $assignments = Assignment::where('semester_id','=', $request->get('semester_id'))->orderBy('start_date','DESC');
        }else{
            $dateNow = date("Y-m-d");
            $semester = Semester::select('id','semester_start_date','semester_end_date')->where('semester_start_date','<=',$dateNow)
                ->where('semester_end_date','>=',$dateNow)->first();
            if(!empty($semester)){
                $assignments = Assignment::select('id','room_id','semester_id','start_date','end_date','technicians_name','phone')->where('semester_id','=',$semester->id)->orderBy('start_date','DESC');
            }else{
                $assignments = Assignment::select('id','room_id','semester_id','start_date','end_date','technicians_name','phone')->orderBy('start_date','DESC');
            }
        }
        if($request->get('room_id')!=null) {
            $assignments = $assignments->where('room_id',$request->get('room_id'))->orderBy('start_date','DESC');
        }

        $assignments = $assignments->get();

        return Datatables::of($assignments)
            ->editColumn('room_id', function ($assignments) {
            $room=Room::find($assignments->room_id)->room_id;
            return $room;
            })
            ->editColumn('semester_id', function ($assignments) {
                $semester=Semester::find($assignments->semester_id)->semester;
                $year=Semester::find($assignments->semester_id)->school_year;
                return "Học kỳ: ".$semester." - Năm học: " .$year;
            })
            ->editColumn('start_date', function ($assignments) {
                $date=date('d / m / Y',strtotime(Assignment::find($assignments->id)->start_date));
                return $date;
            })
           ->editColumn('end_date', function ($assignments) {
               $date=date('d / m / Y',strtotime(Assignment::find($assignments->id)->end_date));
                return $date;
            })
            ->addColumn('action', function ($assignments) {
                return
                    '<a href="" class="btn btn-show btn-info" data-id="' . $assignments->id . '" title="Xem chi tiết" ><i class="fas fa-eye"></i></a>
                    <a href="" class="btn btn-danger btn-delete" data-id="' . $assignments->id . '" title="xóa lịch trực" ><i class="fas fa-trash-alt"></i></a>
                    <a href="" class="btn btn-primary btn-edit" data-id="' . $assignments->id . '" title="sửa lịch trực"><i class="fas fa-edit"></i></a>';
            })
            ->addIndexColumn()
            ->make(true);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $dateNow = date("Y-m-d");

        $idSemester = "";
        $semesters = Semester::select('semester','school_year','id','semester_start_date','semester_end_date')->orderBy('school_year','ASC')->orderBy('semester','ASC')->get();
        $semestersNow = Semester::select('semester','school_year','id','semester_start_date','semester_end_date')->where('semester_start_date','<=',$dateNow)
            ->where('semester_end_date','>=',$dateNow)->first();
        $rooms = Room::select('name', 'id')->where('is_active','=','1')->get();
        if(!empty($semestersNow)) {
            $semester = "Học kỳ: " . $semestersNow->semester . " - Năm học: " . $semestersNow->school_year;
            $idSemester .= $semestersNow->id;
        }else{
            $semester = "";
        }
        return view('backend.assignments.index',[
            'semesters'=>$semesters,
            'rooms'=>$rooms,
            'semestersNow'=> $semester,
            'idSemesterNow' => $idSemester,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $dateNow = date("Y-m-d");
        $idSemester = "";
        $semestersNow = Semester::select('semester','school_year','id','semester_start_date','semester_end_date')->where('semester_start_date','<=',$dateNow)
            ->where('semester_end_date','>=',$dateNow)->first();
        $rooms = Room::select('name', 'id')->where('is_active','=','1')->get();
        if(!empty($semestersNow)) {
            $semester = "Học kỳ: " . $semestersNow->semester . " - Năm học: " . $semestersNow->school_year;
            $idSemester .= $semestersNow->id;
        }else{
            $semester = "";
        }
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
                    'room_id_create'=>'required',
                    'name_user_create'=>'required',
                    'phone_create'=>'required|max:12|min:10',
                    'time_assignment_create'=>'required'
                ]
            );

            if($validator->fails()) {
                return false;
            }

            $start_date = substr( $request->time_assignment_create,  0, 10);
            $end_date = substr( $request->time_assignment_create,  13, 10);
            $start_date = str_replace('/', '-', $start_date);
            $end_date = str_replace('/', '-', $end_date);

            $start_date = date('Y-m-d',strtotime($start_date));
            $end_date = date('Y-m-d',strtotime($end_date));

            //Tạo mới
            $assignment = new Assignment();
            $assignment->technicians_name = $request->name_user_create;
            $assignment->semester_id = $request->id_semester;
            $assignment->room_id = $request->room_id_create;
            $assignment->phone = $request->phone_create;
            $assignment->start_date = $start_date;
            $assignment->end_date = $end_date;
            $assignment->user_create_id = Auth::user()->id;
            $assignment->user_update_id = Auth::user()->id;
            $success = $assignment->save();
            if ($success) {
                $message = 'Thêm mới thành công lịch trực cho phòng máy '.$assignment->room_id;
                return response()->json([
                    'error'     => false,
                    'message'   => $message,
                ]);
            }
        }catch (\Exception $e){
            $message = 'Thêm mới lịch trực thất bại';
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
        $assignment = Assignment::findOrFail($id);
        $room = Room::findOrFail($assignment->room_id);
        $semester = Semester::findOrFail($assignment->semester_id);
        $assignment->room_id = $room->room_id;
        $assignment->semester_id = 'Học kỳ: '.$semester->semester.' - Năm học: '.$semester->school_year;
        $date = date('d/m/Y',strtotime($assignment->start_date))." - ".date('d/m/Y',strtotime($assignment->end_date));
        return response()->json([
            'assignment'=>$assignment,
            'date'     =>$date
        ]);
    }

    public function getTimeOfSemester(Request $request){

        $idSemester = $request->idSemester;
        $start_date = date("Y-m-d");
        $semestersNow = Semester::select('semester','school_year','id','semester_start_date','semester_end_date')->where('semester_start_date','<=',$start_date)
            ->where('semester_end_date','>=',$start_date)->where('id','=',$idSemester)->first();
        if(!empty($semestersNow)) {
            $end_date =  date('d/m/Y',strtotime($semestersNow->semester_end_date));
            $start_date = date("d/m/Y");
        }else{
            $semester = Semester::find($idSemester);
            $end_date =  date('d/m/Y',strtotime($semester->semester_end_date));
            $start_date = date('d/m/Y',strtotime($semester->semester_start_date));
        }

        return response()->json([
            'semester_end_date'=>$end_date,
            'today'     => $start_date,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $assignment = Assignment::findOrFail($id);
        $semester = Semester::select('semester','school_year','semester_start_date','semester_end_date')->find($assignment->semester_id);
        $semesterName = 'Học kỳ: '.$semester->semester.' - Năm học: '.$semester->school_year;
        $date = date('d/m/Y',strtotime($assignment->start_date))." - ".date('d/m/Y',strtotime($assignment->end_date));

        return response()->json([
            'date'          => $date,
            'assignment'    => $assignment,
            'semesterName' => $semesterName,
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
                    'room_id_edit'=>'required',
                    'name_user_edit'=>'required',
                    'phone_edit'=>'required|max:12|min:10',
                    'time_assignment_edit'=>'required'
                ]
            );
            if ($validator->fails()) {
                return false;
            }

            $start_date = substr( $request->time_assignment_edit,  0, 10);
            $end_date = substr( $request->time_assignment_edit,  13, 10);
            $start_date = str_replace('/', '-', $start_date);
            $end_date = str_replace('/', '-', $end_date);

            $start_date = date('Y-m-d',strtotime($start_date));
            $end_date = date('Y-m-d',strtotime($end_date));

            $assignment = Assignment::find($id);
            $assignment->technicians_name = $request->name_user_edit;
            $assignment->room_id = $request->room_id_edit;
            $assignment->phone = $request->phone_edit;
            $assignment->start_date = $start_date;
            $assignment->end_date = $end_date;
            $assignment->user_update_id = Auth::user()->id;
            $success = $assignment->save();
            if ($success) {
                $message = 'Sửa thành công lịch trực cho phòng máy '.$assignment->room_id;
                return response()->json([
                    'error'     => false,
                    'message'   => $message,
                ]);
            }
        }catch (\Exception $e){
            $message = 'Sửa lịch trực thất bại';
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
        $dateNow = date("Y-m-d");
        $assignment = Assignment::findOrFail($id);
        $assignmentCheckUsed = Assignment::select('id','start_date' )->where('id','=',$id)->where('start_date','<=',$dateNow)->get();
        if(count($assignmentCheckUsed)>0) {
            $statusCheck = false;
            $message = 'Lịch trực này đã được sử dụng, không thể xóa lịch trực này';
        }else{
            $message = 'Xóa lịch trực thành công';
            $assignment->delete();
        }
        $statusCheck ? 'true': 'false';
        return response()->json([
            'checkAssignment'        =>$statusCheck,
            'message'                =>$message,
        ]);
    }

    public function checkTimeAssignment(Request $request)
    {
        $statusCheck  = true;
        $id = $request->id;
        $start_date = substr($request->time_assignment, 0, 10);
        $end_date = substr($request->time_assignment, 13, 10);
        $start_date = str_replace('/', '-', $start_date);
        $end_date = str_replace('/', '-', $end_date);
        $room_id = $request->room_id;

        $start_date = date('Y-m-d',strtotime($start_date));
        $end_date = date('Y-m-d',strtotime($end_date));

        if($id == null){
            $check_date_unique = Assignment::select('id','room_id','semester_id','start_date','end_date','technicians_name','phone')
                ->where(function($q) use ($start_date, $room_id){
                    $q->where('start_date','<=',$start_date)
                        ->where('end_date','>=',$start_date)
                        ->where('room_id','=',$room_id);
                })->orWhere(function ($q) use ($start_date, $end_date, $room_id){
                    $q->where('start_date','>=',$start_date)
                        ->where('start_date','<=',$end_date)
                        ->where('room_id','=',$room_id);
                })->get();
        }else{
            $check_date_unique = Assignment::select('id','room_id','semester_id','start_date','end_date','technicians_name','phone')
                ->where(function($q) use ($start_date, $room_id,$id){
                    $q->where('start_date','<=',$start_date)
                        ->where('end_date','>=',$start_date)
                        ->where('room_id','=',$room_id)
                        ->where('id','<>',$id);
                })->orWhere(function ($q) use ($start_date, $end_date, $room_id,$id){
                    $q->where('start_date','>=',$start_date)
                        ->where('start_date','<=',$end_date)
                        ->where('room_id','=',$room_id)
                        ->where('id','<>',$id);
                })->get();
        }
        if(count($check_date_unique)>0) {
            $statusCheck  = false;
        }
        return $statusCheck ? 'true': 'false';
    }
}
