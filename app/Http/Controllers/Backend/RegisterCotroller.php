<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Register;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class RegisterCotroller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
//        dd(Session::get('semester'));
//        dd($request->all());
        $validator = Validator::make($request->all(),
            [
                'MaMH'=>'required|alpha_num',
                'NhomMH'=>'required|numeric',
                'Lop'=>'required|alpha_num',
                'SiSo'=>'required|numeric|max:50|min:1',
                'TietBD'=>'required|numeric|max:13|min:1',
                'SoTiet'=>'required|numeric|max:5|min:6',
                'TuanBD'=>'required|numeric',
                'SoTuan'=>'required|numeric',
            ],
            [
                'required'=>':attribute không được để trống!',
                'numeric'=>':attribute phải là số!',
                'alpha_num'=>':attribute có dạng chữ hoặc số!',
                'max'=>':attribute phải nhỏ hơn hoặc bằng :max!',
                'min'=>':attribute phải có giá trị lớn hơn hoặc bằng :min!'
            ],
            [
                'MaMH'=>'Mã môn học',
                'NhomMH'=>'Nhóm môn học',
                'Lop'=>'Lớp',
                'SiSo'=>'Sĩ số',
                'TietBD'=>'Tiết Bắt đầu',
                'SoTiet'=>'Số tiết',
                'TuanBD'=>'Tuần Bắt đầu',
                'SoTuan'=>'Số tuần',
            ]
            );
        if ($validator->fails()) {
            Session::flash('modal','show');
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $rq = new Register();
        $rq->rooms_id = $request->get('MaPhong');
        $rq->semesters_id = Session::get('semesters_id');
        $rq->subjects_id = Subject::where('subject_id',$request->get('MaMH'))->first()->id;
        $rq->subject_group = $request->get('NhomMH');
        $rq->teacher_id = Auth::user()->user_name;
        $rq->class = $request->get('Lop');
        $rq->amount_people  = $request->get('SiSo');
        $rq->day = $request->get('Thu');
        $rq->session = $request->get('TietBD');
        $rq->number_session = $request->get('SoTiet');
        $rq->week = $request->get('TuanBD');
        $rq->number_week  = $request->get('SoTuan');
        $rq->sender = Auth::user()->id;
        $rq->content = $request->get('LyDo');
        $rq->confirm = 0;
        $rq->user_create_id = Auth::user()->id;
        $rq->user_upadte_id = Auth::user()->id;
        dd($rq);
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
