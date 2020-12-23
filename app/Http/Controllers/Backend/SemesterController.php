<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use Illuminate\Http\Request;
use App\Models\Semester;
use App\Models\Week;
use App\User;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use DateTime;


class SemesterController extends Controller
{
    public function __construct()
    {

    }

    public function getData(Request $request)
    {
        if($request->get('semester')!=null){
            $semesters = Semester::where('semester','=', $request->get('semester'))->orderBy('semester_start_date','DESC');
        }else{
            $semesters = Semester::select('semester','school_year','number_weeks','id','semester_start_date','semester_end_date')->orderBy('semester_start_date','DESC');
        }
        if($request->get('school_year')!=null) $semesters = $semesters->where('school_year','=',$request->get('school_year'))->orderBy('semester','DESC');

        $semesters = $semesters->get();

        return Datatables::of($semesters)
            ->editColumn('semester_start_date', function ($semesters) {
                $i=date('d / m / Y',strtotime($semesters->semester_start_date));
                return $i;
            })
            ->editColumn('semester_end_date', function ($semesters) {
                $i=date('d / m / Y',strtotime($semesters->semester_end_date));
                return $i;
            })
            ->addColumn('action', function ($semester) {
                return
                '<a href="/admin/semester/show/'. $semester->id.'" class="btn btn-info btn-show" data-id="' . $semester->id . '" title="Danh sách tuần" ><i class="fas fa-eye"></i></a>
                 <a href="" class="btn btn-danger btn-delete" data-id="' . $semester->id . '" title="xóa học kỳ" ><i class="fas fa-trash-alt"></i></a>
                 <a href="" class="btn btn-primary  btn-edit" data-id="' . $semester->id . '" title="sửa học kỳ"><i class="fas fa-edit"></i></a>';
            })
            ->addIndexColumn()
            ->rawColumns(['action'])
            ->make(true);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $school_year = array();
        $school_years = Semester::select('school_year')->orderBy('school_year','ASC')->get();
        foreach ($school_years as $tg){
            array_push($school_year,$tg->school_year);
        }
        $yearNow = date("Y");
        --$yearNow;
        $school_year = array_unique($school_year);
        return view('backend.semester.index',[
            'school_year'   =>  $school_year,
            'yearNow_add'   =>  $yearNow,
            'yearNow_edit'  =>  $yearNow,
        ]);
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
            $start_year = substr($request->school_year, 0, 4);
            $end_year = substr($request->school_year, 7, 4);

            //check validetor
            $validator = Validator::make($request->all(),
                [
                    'number_weeks'=>'required|numeric|max:60|min:2',
                    'semester_start_date'=>'required',
                ]
            );

            if ($validator->fails()) {
                return false;
            }

            //Tính ngày bắt đầu và ngày kết thúc của học kỳ
            $semester_start_date = str_replace('/', '-', $request->semester_start_date);
            $semester_start_date = date("Y-m-d", strtotime($semester_start_date));
            $count_day = date("N",strtotime($semester_start_date)); //Lấy ra ngày trong tuần đầu tiên
            $semester_end_date = strtotime($semester_start_date . " + $request->number_weeks week - $count_day day");
            $semester_end_date = strftime("%Y-%m-%d", $semester_end_date);

            //Thêm học kỳ
            $semester = new Semester;
            $semester->semester = $request->semester;
            $semester->number_weeks = $request->number_weeks;
            $semester->school_year = $request->school_year;
            $semester->semester_start_date = $semester_start_date;
            $semester->semester_end_date = $semester_end_date;
            $semester->user_create_id = Auth::user()->id;
            $semester->user_update_id = Auth::user()->id;
            $success = $semester->save();

            //Thêm tuần
            $date = 7 - date("N",strtotime($semester_start_date)); //Lấy số ngày còn thiếu trong tuần đầu tiên
            for ($i = 1; $i <= $request->number_weeks; $i++) {
                if($i==1){
                    $start_Date = 0;
                    $end_Date = $date;
                }
                else {
                    $start_Date = $date + ($i - 2) * 7 + 1;
                    $end_Date = $date + ($i - 1) * 7 ;
                }

                $week = new Week;
                $week->semester_id = $semester->id;
                $week->week = $i;
                $week->start_day = strftime("%Y-%m-%d", strtotime($semester->semester_start_date . " + $start_Date days"));
                $week->end_day = strftime("%Y-%m-%d", strtotime($semester->semester_start_date . " + $end_Date days"));
                $week->user_create_id = Auth::user()->id;
                $week->user_update_id = Auth::user()->id;
                $week->save();
            }
            if ($success)
            {
                $message = 'Thêm mới thành công học kỳ: '. $semester->semester .' - Năm học: '. $semester->school_year;
                return response()->json([
                    'error'     => false,
                    'message'   => $message,
                ]);
            }

        }catch (\Exception $e){
            $message = 'Thêm mới thất bại học kỳ';
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
       return view('backend.semester.show')->with([
           'semester_id'=>$id
       ]);
    }

    public function getWeek($id)
    {
        $week = Week::all()->where('semester_id','=',$id);
        return Datatables::of($week)
            ->editColumn('start_day', function ($week) {
                $i=date('d / m / Y',strtotime($week->start_day));
                return $i;
            })
            ->editColumn('end_day', function ($week) {
                    $i=date('d / m / Y',strtotime($week->end_day));
                    return $i;
            })
            ->addColumn('action', function ($week) {
                return
                    '<a href="" data-id="' . $week->id . '" class="btn show-modal btn-info" title="Xem chi tiết" ><i class="fas fa-eye"></i></a>
                     <a href="' . $week->id . '" data-id="' . $week->id . '" class="btn btn-success btn-edit" title="Thêm ghi chú" ><i class="fas fa-plus"></i></a>';
            })
            ->make(true);
    }

    public function showWeek($id)
    {
        $week=Week::findOrFail($id);
        return response()->json([
            'week'=>$week
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

        $semester = Semester::select('id', 'semester', 'school_year', 'number_weeks', 'semester_start_date')->where('id', '=', $id)->get()->toArray();
        $semester[0]['school_year'] = substr( $semester[0]['school_year'],  0, 4);
        $semester[0]['semester_start_date'] = date('d/m/Y', strtotime($semester[0]['semester_start_date']));
        $yearNow = date("Y");
        --$yearNow;
        return response()->json([
            'error' => false,
            'semester' => $semester,
            'yearNow' => $yearNow,
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
            $start_year = substr($request->school_year, 0, 4);
            $end_year = substr($request->school_year, 7, 4);

            //Kiểm tra lỗi
            $validator = Validator::make($request->all(),
                [
                    'number_weeks'=>'required|numeric|max:60|min:2',
                    'semester_start_date'=>'required',
                ]
            );

            if ($validator->fails()) {
                return false;
            }

            //Kiểm tra với trường hợp đang sửa học kì
            $semesters = Semester::select('id','semester', 'school_year', 'semester_start_date', 'semester_end_date')
                ->where('semester', '=', $request->semester)
                ->where('school_year', '=', $request->school_year)
                ->where('id','<>',$id)->get();
            if(count($semesters)>0) return false;

            //Tính ngày bắt đầu và ngày kết thúc của học kỳ
            $semester_start_date = str_replace('/', '-', $request->semester_start_date);
            $semester_start_date = date("Y-m-d", strtotime($semester_start_date));
            $count_day = date("N",strtotime($semester_start_date)); //Lấy ra ngày trong tuần đầu tiên
            $semester_end_date = strtotime($semester_start_date . " + $request->number_weeks week - $count_day days");
            $semester_end_date = strftime("%Y-%m-%d", $semester_end_date);

            //Cập nhật lại dữ liệu
            $semester = Semester::find($id);
            $semester->semester = $request->semester;
            $semester->school_year = $request->school_year;
            $semester->number_weeks = $request->number_weeks;
            $semester->semester_start_date = $semester_start_date;
            $semester->semester_end_date = $semester_end_date;
            $semester->user_update_id = Auth::user()->id;
            $success = $semester->save();

            // xóa tuần có mã học kỳ = id
            $weeks = Week::where('semester_id', '=', $id)->get();
            foreach ($weeks as $item) $item->delete();

            // thêm tuần
            $date = 7 - date("N",strtotime($semester_start_date)); //Lấy số ngày còn thiếu trong tuần đầu tiên
            for ($i = 1; $i <= $request->number_weeks; $i++) {
                if($i==1){
                    $start_Date = 0;
                    $end_Date = $date;
                }
                else {
                    $start_Date = $date + ($i - 2) * 7 + 1;
                    $end_Date = $date + ($i - 1) * 7 ;
                }
                $week = new Week;
                $week->semester_id = $id;
                $week->week = $i;
                $week->start_day = strftime("%Y-%m-%d", strtotime($semester_start_date . " + $start_Date days"));
                $week->end_day = strftime("%Y-%m-%d", strtotime($semester_start_date . " + $end_Date days"));
                $week->user_create_id = Auth::user()->id;
                $week->user_update_id = Auth::user()->id;
                $week->save();
            }

            if ($success)
            {
                $message = 'Cập nhật thành công thông tin học kỳ: '. $semester->semester .' - Năm học: '.$semester->school_year;
                return response()->json([
                    'error'     => false,
                    'message'   => $message,
                ]);
            }
        }catch (\Exception $e){
            $message = 'Cập nhật thất bại thông tin học kỳ';
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
        $check_IsEmpty_Semester = true;
        $semester = Semester::findOrFail($id);
        $semester_isEmpty_Assignment =  Assignment::select('semester_id')->where('semester_id','=',$id)->get();
        if(count($semester_isEmpty_Assignment)>0) {
            $check_IsEmpty_Semester  = false;
        }else{
            $semester->delete();

            //xóa tuần có mã học kỳ = id
            $weeks = Week::where('semester_id', '=', $id)->get();
            foreach ($weeks as $item) $item->delete();
        }
        $check_IsEmpty_Semester ? 'true': 'false';
        $message = 'Học kỳ '.$semester->semester.' - Năm học '.$semester->school_year;
        return response()->json([
            'check_IsEmpty_Semester'=>$check_IsEmpty_Semester,
            'message'                =>$message,
        ]);

    }

    public function getNote($id)
    {
        $note=Week::select('note')->where('id','=',$id)->first();
        return response()->json([
            'note'=>$note
        ]);
    }

    public function saveNote(Request $request, $id)
    {
        $week = Week::findOrFail($id);
        $week->note = $request->get('note');
        $week->user_update_id=Auth::user()->id;
        $week->save();
        die("q");
    }

    public function checkTime(Request $request)
    {
        $id = $request->id;

        $status_check  = true;
        $start_date = str_replace('/', '-', $request->semester_start_date);

        //Tính ngày bắt đầu và ngày kết thúc của học kỳ
        $start_date = date("Y-m-d", strtotime($start_date));
        $count_day = date("N",strtotime($start_date)); //Lấy ra ngày trong tuần đầu tiên
        $end_date = strtotime($start_date . " + $request->number_weeks week - $count_day day");
        $end_date = strftime("%Y-%m-%d", $end_date);

        if($id == null){
            //Kiểm tra thời gian của học kỳ có bị trùng hay không khi thêm mới
            $check_date_semester = Semester::where(function($q) use ($start_date){
                $q->where('semester_start_date','<=',$start_date)
                    ->where('semester_end_date','>=',$start_date);
            })->orWhere(function ($q) use ($start_date, $end_date){
                $q->where('semester_start_date','>=',$start_date)
                    ->where('semester_start_date','<=',$end_date);
            })->get();
        }
        else{
            //Kiểm tra thời gian của học kỳ có bị trùng hay không khi chỉnh sửa
            $check_date_semester = Semester::where(function($q) use ($start_date, $id){
                $q->where('semester_start_date','<=',$start_date)
                    ->where('semester_end_date','>=',$start_date)
                ->where('id','<>',$id);
            })->orWhere(function ($q) use ($start_date, $end_date, $id){
                $q->where('semester_start_date','>=',$start_date)
                    ->where('semester_start_date','<=',$end_date)
                    ->where('id','<>',$id);
            })->get();
        }

        if(count($check_date_semester)>0) {
            $status_check  = false;
        }
        return $status_check ? 'true': 'false';
    }

    public function checkSemesterUnique(Request $request)
    {
        $status_check  = true;
        $id = $request->id;

        if($id == null){
            //Kiểm tra với trường hợp đang tạo mới học kì
            $semesters = Semester::select('semester', 'school_year', 'semester_start_date', 'semester_end_date')
                ->where('semester', '=', $request->semester)
                ->where('school_year', '=', $request->school_year)->get();
        }
        else{
            //Kiểm tra với trường hợp đang sửa học kì
            $semesters = Semester::select('id','semester', 'school_year', 'semester_start_date', 'semester_end_date')
                ->where('semester', '=', $request->semester)
                ->where('school_year', '=', $request->school_year)
                ->where('id','<>',$id)->get();
        }

        if(count($semesters)>0) {
            $status_check  = false;
        }
        return $status_check ? 'true': 'false';
    }
}
