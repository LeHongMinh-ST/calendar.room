<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Room;
use App\Models\Schedule;
use App\Models\Semester;
use App\Models\Subject;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use function Sodium\add;

class StatisticsController extends Controller
{
    public function getDataNumberSession(Request $request)
    {
        $listSchedule = $this->getSchedules($request);

        return Datatables::of($listSchedule)
            ->editColumn('subject_id', function ($listSchedule) {
                $subjectId=Subject::find($listSchedule['subject_id'])->subject_id;
                return $subjectId;
            })
            ->editColumn('room_id', function ($listSchedule) {
                $roomId=Room::find($listSchedule['room_id'])->id;
                return $roomId;
            })
            ->editColumn('number_session', function ($listSchedule) {
                $numberSession = $listSchedule['number_session'] * $listSchedule['number_week'];
                return $numberSession;
            })
            ->editColumn('sum_session', function ($listSchedule) {
                $sumSession = $listSchedule['number_session'] * $listSchedule['number_week'] * $listSchedule['amount_people'];
                return $sumSession;
            })
            ->addIndexColumn()
            ->make(true);
    }

    public function getDataSubjectGroup(Request $request){
        $listSchedule = $this->getSchedules($request);

        return Datatables::of($listSchedule)
            ->editColumn('room_id', function ($listSchedule) {
                $roomId=Room::find($listSchedule['room_id'])->room_id;
                return $roomId;
            })
            ->editColumn('subject_group', function ($listSchedule) {
                $numberSession = $listSchedule['subject_group'];
                return $numberSession;
            })
            ->editColumn('class', function ($listSchedule) {
                $numberSession = $listSchedule['class'];
                return $numberSession;
            })
            ->addIndexColumn()
            ->make(true);
    }

    public function getSchedules(Request $request){
        $listSemester = Semester::select('id')->where('school_year','=',$request->get('year_School'))->get()->toArray();
        $listSemester = array_map('current', $listSemester);
        $listSchedule = Schedule::whereIn('semester_id', $listSemester);

        if($request->get('semester') != null){
            $listSchedule = Schedule::where('semester_id',$request->get('semester'));
        }

        if($request->get('room_id') != null){
            $listSchedule->where('room_id',$request->get('room_id'));
        }

        if($request->get('faculty_id') != null){
            $listDepartment = Department::select('id')->where('faculty_id',$request->get('faculty_id'))->get()->toArray();
            $listDepartment = array_map('current', $listDepartment);
            $listSubject = Subject::select('id')->whereIn('department_id',$listDepartment)->get()->toArray();
            $listSubject = array_map('current', $listSubject);
            $listSchedule->whereIn('subject_id',$listSubject);
        }
        $listSchedule = $listSchedule->get()->toArray();

        return $listSchedule;
    }

    public function statisticsSubjectGroup(){
        $rooms = Room::select('id','name')->get();
        $faculties = Faculty::select('id','name')->get();
        $yearSchool = [];
        $semesters = Semester::select('id','school_year','semester_start_date','semester_end_date')->orderBy('semester_start_date','DESC')->limit(9)->get()->toArray();
        foreach ($semesters as $semester) {
            array_push($yearSchool, $semester['school_year']);
        }
        $yearSchool = array_unique($yearSchool);

        return view('backend.statistics.subjectGroup',[
            'semesters'     => $semesters,
            'rooms'         => $rooms,
            'faculties'     => $faculties,
            'yearSchool'       => $yearSchool,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rooms = Room::select('id','name')->get();
        $faculties = Faculty::select('id','name')->get();
        $yearSchool = [];
        $semesters = Semester::select('id','school_year','semester_start_date','semester_end_date')->orderBy('semester_start_date','DESC')->limit(9)->get()->toArray();
        foreach ($semesters as $semester) {
            array_push($yearSchool, $semester['school_year']);
        }
        $yearSchool = array_unique($yearSchool);

        return view('backend.statistics.numberSession',[
            'semesters'     => $semesters,
            'rooms'         => $rooms,
            'faculties'     => $faculties,
            'yearSchool'       => $yearSchool,
        ]);
    }

    public function getSemester(Request $request){
        $listSemester = Semester::select('id','semester')->where('school_year',$request->get('year_School'))->get()->toArray();
        return response()->json([
            'listSemester' => $listSemester,
        ]);
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
