<?php

use Illuminate\Database\Seeder;

class RoomsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('rooms')->truncate();

        DB::table('rooms')->insert([
            'room_id'=>'THCNTT01',
            'name'=>'Thực hành công nghệ thông tin 01',
            
            'computer_number'=>'28',
            'address'=>'Tầng 03 - Nhà Hành Chính',
            'subject'=>'Kiến trúc máy tính và vi xử lý   , Kiến trúc máy tính   , Mạng máy tính   , LT toán học với Mathematica   , ',
            'software'=>'',
            'is_active'=>'1',
            'user_create_id'=>'1',
            'user_update_id'=>'1',

        ]);
        DB::table('rooms')->insert([
            'room_id'=>'THCNTT02',
            'name'=>'Thực hành công nghệ thông tin 02',
            
            'computer_number'=>'33',
            'address'=>'Tầng 03 - Nhà Hành Chính',
            'subject'=>'Kiến trúc máy tính và vi xử lý   , Kiến trúc máy tính   , Mạng máy tính   , LT toán học với Mathematica   , ',
            'software'=>'',
            'is_active'=>'1',
            'user_create_id'=>'1',
            'user_update_id'=>'1',

        ]);
        DB::table('rooms')->insert([
            'room_id'=>'THCNTT03',
            'name'=>'Thực hành công nghệ thông tin 03',
            
            'computer_number'=>'22',
            'address'=>'Tầng 03 - Nhà Hành Chính',
            'subject'=>'Kiến trúc máy tính và vi xử lý   , Kiến trúc máy tính   , Mạng máy tính   , LT toán học với Mathematica   , ',
            'software'=>'',
            'is_active'=>'1',
            'user_create_id'=>'1',
            'user_update_id'=>'1',

        ]);
        DB::table('rooms')->insert([
            'room_id'=>'THCNTT06',
            'name'=>'Thực hành công nghệ thông tin 06',
            
            'computer_number'=>'54',
            'address'=>'Tầng 03 - Nhà Hành Chính',
            'subject'=>'Kiến trúc máy tính và vi xử lý   , Kiến trúc máy tính   , Mạng máy tính   , LT toán học với Mathematica   , ',
            'software'=>'',
            'is_active'=>'1',
            'user_create_id'=>'1',
            'user_update_id'=>'1',

        ]);
        DB::table('rooms')->insert([
            'room_id'=>'THCNTT07',
            'name'=>'Thực hành công nghệ thông tin 07',
            
            'computer_number'=>'60',
            'address'=>'Phòng 211 Giảng đường Nguyễn Đăng',
            'subject'=>'Kiến trúc máy tính và vi xử lý   , Kiến trúc máy tính   , Mạng máy tính   , LT toán học với Mathematica   , ',
            'software'=>'',
            'is_active'=>'1',
            'user_create_id'=>'1',
            'user_update_id'=>'1',

        ]);
        DB::table('rooms')->insert([
            'room_id'=>'THCNTT08',
            'name'=>'Thực hành công nghệ thông tin 08',
            
            'computer_number'=>'20',
            'address'=>'Phòng 310 Giảng đường Nguyễn Đăng',
            'subject'=>'Kiến trúc máy tính và vi xử lý   , Kiến trúc máy tính   , Mạng máy tính   , LT toán học với Mathematica   , ',
            'software'=>'',
            'is_active'=>'1',
            'user_create_id'=>'1',
            'user_update_id'=>'1',

        ]);
        DB::table('rooms')->insert([
            'room_id'=>'THCNTT09',
            'name'=>'Thực hành công nghệ thông tin 09',
            
            'computer_number'=>'60',
            'address'=>'Phòng 310 Giảng đường Nguyễn Đăng',
            'subject'=>'Kiến trúc máy tính và vi xử lý   , Kiến trúc máy tính   , Mạng máy tính   , LT toán học với Mathematica   , ',
            'software'=>'',
            'is_active'=>'1',
            'user_create_id'=>'1',
            'user_update_id'=>'1',
        ]);
    }
}
