<?php

namespace App\Imports;

use App\Models\Room;
use App\Models\Subject;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ScheduleImport implements ToCollection, WithHeadingRow
{
    public function __construct()
    {
    }
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\ToCollection|null
    */


    public function collection(Collection  $rows)
    {

        DB::beginTransaction();
        try {

            $semester_id    = Session::get('semesters')['id'];

            foreach ($rows as $row)
            {

                $room = Room::select('id','room_id')->where('room_id',$row['f_tenph'])->first();
                $subject = Subject::select('id','subject_id')->where('subject_id',$row['f_mamh'])->first();
                $user = User::select('id','user_name')->where('user_name',$row['f_manv'] ? $row['f_manv'] : 'gv')->first();
                $weekCheck = $this->getWeek($row->toArray());

                $room_id = null;
                $subject_id = null;

                if($room == null){
                    $room                   = new Room();
                    $room->room_id          = $row['f_tenph'];
                    $room->name             = 'Phòng thực hành' . $row['f_tenph'];
                    $room->user_create_id   = Auth::user()->id;
                    $room->user_update_id   = Auth::user()->id;
                    $room->save();
                }

                if($subject == null){
                    $subject                    = new Subject();
                    $subject->subject_id        = $row['f_mamh'];
                    $subject->name              = $row['f_tenmhvn'];
                    $subject->user_create_id    = Auth::user()->id;
                    $subject->user_update_id    = Auth::user()->id;
                    $subject->save();
                }

                if($user == null ){
                    $user = new User();
                    $user->user_name = $row['f_manv'];
                    $user->password = Hash::make(env('PASSWORD_USER',12345678));
                    $user->full_name = $row['f_holotcbv'] .' '. $row['f_tencbv'];
                    $user->role_id = 0;
                    $user->is_active = 1;
                    $user->user_create_id    = Auth::user()->id;
                    $user->user_update_id    = Auth::user()->id;
                    $user->save();
                }

                if(!Schedule::where([
                    'room_id'=>$room->id,
                    'semester_id'=>$semester_id,
                    'day'=>$row['f_thu'],
                    'session'=>$row['f_tietbd'],
                    'week'=>$weekCheck[0]
                ])->exists()){

                    $status = 0;

                    if(Auth::user()->role_id == 1){
                        $status = 1;
                    }


                    Schedule::create([
                        'room_id' => $room->id,
                        'subject_id' => $subject->id,
                        'teacher_id' => $user->user_name,
                        'semester_id' => $semester_id,
                        'subject_group' => $row['f_manh'] ? $row['f_manh'] : 0,
                        'class' => $row['f_malp'],
                        'amount_people' => $row['f_sisoctgh'],
                        'day' => $row['f_thu'],
                        'session'=> $row['f_tietbd'],
                        'number_session'=> $row['f_sotiet'],
                        'week' => $weekCheck[0],
                        'number_week' => count($weekCheck),
                        'week_check' => implode(',',$weekCheck),
                        'status' => $status,
                        'user_create_id' => Auth::user()->id,
                        'user_update_id' => Auth::user()->id,
                    ]);
                }


            }
            DB::commit();
            return true;

        }catch (\Exception $e){
            dd($e->getMessage());
            DB::rollBack();
            return false;
        }
    }

    public function getWeek(array $rows){
        $tRows = array_keys(array_filter(
            $rows,
            function ($val, $key) {
                return (preg_match("/t\d+/", $key)) && ($val === 'x');
            },
            ARRAY_FILTER_USE_BOTH
        ));

        $result = [];

        foreach ($tRows as $tRow){
            $result[] = substr($tRow,1);
        }

        return $result;
    }
}
