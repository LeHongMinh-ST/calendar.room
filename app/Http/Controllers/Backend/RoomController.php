<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Room;
use App\Models\Semester;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class RoomController extends Controller
{

    public function getData()
    {
        $rooms = Room::all();
        return Datatables::of($rooms)
            ->editColumn('is_active', function ($room){
                $string ='';
                if($room->is_active == 1){
                    $string .=   '<form action="" method="post">
                                        <label class="switch">
                                            <input class="active_switch" type="checkbox" id="active_switch' . $room->id . '" checked="checked" data-id="' . $room->id . '">
                                            <span class="slider round"></span>
                                        </label>
                                    </form>';
                }else{
                    $string .=   '<form action="" method="post">
                                        <label class="switch">
                                            <input class="active_switch" type="checkbox" id="active_switch' . $room->id . '" data-id="' . $room->id . '">
                                            <span class="slider round"></span>
                                        </label>
                                    </form>';
                }
                return $string;
            })
            ->addColumn('action', function ($room) {
                $string ='';
                $string .=
                    '
                    <a href="' . $room->id . '" data-id="' . $room->id . '" class="btn btn-info show-modal" title="Xem chi tiết" ><i class="fas fa-eye"></i></a>
                    <a href="" data-id="' . $room->id . '" class="btn btn-danger btn-delete" title="xóa phòng máy" ><i class="fas fa-trash-alt"></i></a>
                    <a href="' . $room->id . '" data-id="' . $room->id . '" class="btn btn-primary  btn-edit" title="sửa thông tin phòng máy"><i class="fas fa-edit"></i></a>';
                return $string;
            })
            ->addIndexColumn()
            ->rawColumns(['is_active', 'action'])
            ->make(true);
    }

    public function status($id)
    {
        $room = Room::findOrFail($id);
        if ($room->is_active == 1)
            $room->is_active = 0;
        else
            $room->is_active = 1;
        $room->save();

        return response()->json([
            'room'=>$room->room_id
        ]);
    }

    /**
     * <p> Display a listing of the resource. </p>
     *
     * @return \Illuminate\Http\Response The {@link  \Illuminate\Http\Response}
     */
    public function index()
    {
        return view('backend.rooms.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            $subjects = Subject::select('id','subject_id','name')->get()->toArray();
            $message = 'Lấy dữ liệu môn học thành công';
            return response()->json([
                'error'     => false,
                'subjects'=>$subjects,
                'message'   => $message,
            ]);

        }catch (\Exception $e){
            $message = 'Lấy dữ liệu môn học thất bại';
            return response()->json([
                'error' => true,
                'message'   => $message,
            ]);
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
            //Kiem tra loi
            $validator = Validator::make($request->all(),
                [
                    'room_id'=>'required|unique:rooms,deleted_at,NULL|max:10',
                    'name'=>'required|unique:rooms,deleted_at,NULL|max:60',
                    'computer_number'=>'required|numeric|min:10|max:100',
                    'address'=>'required'
                ]
            );
            if ($validator->fails()) {
                return false;
            }

            //Chuyen doi chu thuong sang chu hoa
            $room_id = strtoupper($request->room_id);

            //Tao list mon hoc
            $listSubject = "";
            foreach ($request->subjects as $subject)
            {
                $listSubject = $listSubject .$subject.', ';
            }
            $listSubject = substr($listSubject, 0, -2);

            //Luu lai phong may
            $room = new Room;
            $room->room_id = $room_id;
            $room->name = ucfirst($request->name);
            $room->computer_number = $request->computer_number;
            $room->address = ucfirst($request->address);
            $room->subject = $listSubject;
            $room->software = ucfirst($request->software);
            $room->user_create_id = Auth::user()->id;
            $room->user_update_id = Auth::user()->id;
            $success = $room->save();

            if ($success)
            {
                $message = 'Thêm mới thành công phòng máy '. $room->room_id;
                return response()->json([
                    'error'     => false,
                    'message'   => $message,
                ]);
            }

        }catch (\Exception $e){
            $message = 'Thêm mới phòng máy thất bại';
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
        $room = Room::findOrFail($id);
        return response()->json([
            'room'=>$room
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
        try {
            $room = Room::findOrFail($id)->toArray();
            $listSubject = explode(", ", $room['subject']);

            $subjects = Subject::select('subject_id','name')->get()->toArray();
            return response()->json([
                'error' => false,
                'room' =>$room,
                'subjects'=>$subjects,
                'listSubject' => $listSubject,
            ]);

        }catch (\Exception $e){
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
            ]);
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
            //Kiem tra loi
            $validator = Validator::make($request->all(),
                [
                    'room_id'=>'required|alpha_num|max:10',
                    'name'=>'required|max:40',
                    'computer_number'=>'required|numeric|min:10|max:100',
                    'address'=>'required'

                ]
            );
            if ($validator->fails()) {
                return false;
            }

            //Kiểm tra tên phòng máy là duy nhất
            $rooms = Room::select('id','name')
                ->where('name', '=', $request->name)
                ->where('id','<>',$id)->get();
            if(count($rooms)>0) return false;

            //Kiểm tra mã phòng máy là duy nhất
            $rooms = Room::select('id','room_id')
                ->where('room_id', '=', $request->room_id)
                ->where('id','<>',$id)->get();
            if(count($rooms)>0) return false;

            //Chuyen doi chu thuong sang chu hoa
            $room_id = strtoupper($request->room_id);

            //Tao list mon hoc
            $listSubject = "";
            foreach ($request->subjects as $subject)
            {
                $listSubject = $listSubject .$subject.', ';
            }
            $listSubject = substr($listSubject, 0, -2);

            //Luu lai phong may
            $room = Room::findOrFail($id);
            $room->room_id = $room_id;
            $room->name =ucfirst($request->name);
            $room->computer_number = $request->computer_number;
            $room->address = ucfirst($request->address);
            $room->subject = $listSubject;
            $room->software = ucfirst($request->software);
            $room->user_update_id = Auth::user()->id;
            $success = $room->save();

            if ($success)
            {
                $message = 'Cập nhật thành công thông tin phòng máy '. $room->room_id;
                return response()->json([
                    'error'     => false,
                    'message'   => $message,
                ]);
            }
        }catch (\Exception $e){
            $message = 'Cập nhật thông tin phòng máy thất bại';
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
        $check_IsEmpty_Room = true;
        $room = Room::findOrFail($id);
        $room_isEmpty_Assignment =  Assignment::select('room_id')->where('room_id','=',$id)->get();
        if(count($room_isEmpty_Assignment)>0) {
            $check_IsEmpty_Room  = false;
        }else{
            $room->delete();
        }
        $check_IsEmpty_Room ? 'true': 'false';
        return response()->json([
            'check_IsEmpty_Room'=>$check_IsEmpty_Room,
            'room_id'           =>$room->room_id,
        ]);
    }

    public function checkRoomIdUnique(Request $request)
    {
        $status_check  = true;
        $id = $request->id;

        if($id == null){
            //Kiểm tra với trường hợp đang tạo mới phòng máy
            $rooms = Room::select('room_id')
                ->where('room_id', '=', $request->room_id)->get();
        }
        else{
            //Kiểm tra với trường hợp đang sửa phòng máy
            $rooms = Room::select('id','room_id')
                ->where('room_id', '=', $request->room_id)
                ->where('id','<>',$id)->get();
        }

        if(count($rooms)>0) {
            $status_check  = false;
        }
        return $status_check ? 'true': 'false';
    }

    public function checkNameRoomUnique(Request $request)
    {
        $status_check  = true;
        $id = $request->id;

        if($id == null){
            //Kiểm tra với trường hợp đang tạo mới phòng máy
            $rooms = Room::select('name')
                ->where('name', '=', $request->name)->get();
        }
        else{
            //Kiểm tra với trường hợp đang sửa phòng máy
            $rooms = Room::select('id','name')
                ->where('name', '=', $request->name)
                ->where('id','<>',$id)->get();
        }

        if(count($rooms)>0) {
            $status_check  = false;
        }
        return $status_check ? 'true': 'false';
    }
}
