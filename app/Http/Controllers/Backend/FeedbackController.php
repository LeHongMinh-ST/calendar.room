<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Feedback;
use App\Models\Handle_feedback;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class FeedbackController extends Controller
{
    public function getData(Request $request)
    {
        if($request->get('handle')==2){
            $feedbacks = Feedback::where('deleted_at','!=', null);
        }else{
            $feedbacks = Feedback::where('handle',$request->get('handle'));
        }
        if($request->get('room_id')!=null) $feedbacks = $feedbacks->where('room_id',$request->get('room_id'));

        $feedbacks = $feedbacks->get();
        return DataTables::of($feedbacks)
            ->addColumn('room_id', function ($feedback) {
                $i=Room::find($feedback->room_id)->name;
                return $i;
            })
            ->addColumn('created_at', function ($feedback) {
                $i=strftime("%d -%m -%Y", strtotime($feedback->created_at));;
                return $i;
            })
            ->addColumn('user_create', function ($feedback) {
                $i=User::find($feedback->user_create_id)->full_name;
                return $i;
            })
            ->addColumn('status', function ($feedback) {
                $i = ($feedback->handle == 1) ?  '<p style="color: #0E9A00">Đã xử lí</p>' : '<p style="color: black ">Chưa xử lí</p>';
                if($feedback->deleted_at != null) $i= "Đã gỡ phản ánh";
                return $i;
            })
            ->addColumn('action', function ($feedback) {
                $action='
                        <a href="" data-id="' . $feedback->id . '" class="btn btn-primary btn-icon btn-show" title="Xem phản ánh"> <i class="fas fa-eye"></i></a>
                        ';
                if(Auth::user()->id == $feedback->user_id && $feedback->handle == 0){
                    $action.='<a href="" data-id="' . $feedback->id . '" class="btn btn-danger btn-delete" title="Gỡ phản ánh"> <i class="fas fa-trash"></i></a>';
                }
                return $action;
            })
            ->rawColumns(['action','status'])
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
        $rooms=Room::select('id','name')->orderBy('id','ASC')->get()->toArray();

        return view('backend.feedbacks.index')->with([
            'rooms'=>$rooms
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $rooms=Room::select('id','name')->orderBy('id','ASC')->get()->toArray();

        return view('backend.feedbacks.create')->with([
            'rooms'=>$rooms
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
        try {
            $validate= Validator::make($request->all(),[
                'room_id' => 'required',
                'content' => 'required|min:5',

            ],[
                'required' => ':attribute Không được để trống',
                'min' => ':attribute Không được nhỏ hơn :min',
            ],[
                'room_id' => 'Phòng máy',
                'content'=>'Nội dung phản ánh',
            ]);
            if ($validate->fails()) {
                return redirect()->back()->withInput()->withErrors($validate);
            }
            $feedback=new Feedback();
            $feedback->room_id=$request->get('room_id');
            $feedback->content=$request->get('content');
            $feedback->handle=0; //0 là tình trạng chưa xử lí
            $feedback->user_create_id=Auth::user()->id;
            $feedback->user_update_id=Auth::user()->id;
            $feedback->user_id=Auth::user()->id;
            $success=$feedback->save();
            if ($success)
                Session::put('success_feedback', 'Thêm mới thành công một phản ánh ');
            return redirect()->route('calendar');
        } catch (ModelNotFoundException $exception) {
            return back()->withError($exception->getMessage())->withInput();
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
        try {
            $feedback=Feedback::findOrFail($id);
            $room=Room::findOrFail($feedback->room_id)->name;
            $user=User::findOrFail($feedback->user_id)->full_name;
            $time=strftime("%d -%m -%Y", strtotime($feedback->created_at));
            if ($feedback->handle == 1){
                $status = 'Đã xử lí';
                $handle_feedback = Handle_feedback::where('feedback_id', $feedback->id)->first();
                $button = '<a href="" data-id="' . $handle_feedback->id. '" class="btn btn-primary btn-icon btn-show_handle"> Xem xử lí</a>';

            }else{
                $status = 'Chưa xử lí';
                if (Auth::user()->role_id > 0 ){
                    $button = '<a href="" data-id="' . $feedback->id . '" class="btn btn-primary btn-icon btn-handle"> Xử lí phản ánh</a>';
                }else{
                    $button = '';
                }
            }

            return response()->json([
                'feedback'=>$feedback,
                'room'=>$room,
                'user'=>$user,
                'time'=>$time,
                'status'=>$status,
                'button'=>$button
            ]);
        } catch (ModelNotFoundException $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $feedback = Feedback::findOrFail($id);
            $rooms=Room::select('id','name')->get()->toArray();
            return view('backend.feedbacks.edit')->with([
                'feedback' => $feedback,
                'rooms' => $rooms
            ]);
        } catch (ModelNotFoundException $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }

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
            $validate= Validator::make($request->all(),[
                'content' => 'required|min:5',

            ],[
                'min' => ':attribute Không được nhỏ hơn :min',
            ],[
                'content'=>'Nội dung phản ánh',
            ]);
            if ($validate->fails()) {
                return redirect()->back()->withInput()->withErrors($validate);
            }
            $feedback = Feedback::findOrFail($id);
            $feedback->room_id = $request->get('room_id');
            $feedback->content = $request-> get('content');
            $feedback->user_update_id=Auth::user()->id;
            $success=$feedback->save();
            if ($success)
                Session::put('success', 'Thêm mới thành công một phản ánh ');
            return redirect()->route('feedback.index');
        } catch (ModelNotFoundException $exception) {
            return back()->withError($exception->getMessage())->withInput();
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
        try {
            $feedback=Feedback::findOrFail($id);
            $feedback->delete();
        } catch (ModelNotFoundException $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }

    }
}
