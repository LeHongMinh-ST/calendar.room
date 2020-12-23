<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->truncate();

        DB::table('users')->insert([
            'id'=>1,
            'user_name'=>'admin',
            'full_name'=>'admin',
            'phone'=>'0961130648',
            'email'=>'admin@gmail.com',
            'department_id'=>1,
            'role_id'=>1,
            'note'=>'abc11111',
            'is_active'=>1,
            'password'=>\Illuminate\Support\Facades\Hash::make(12345678),
            'user_create_id'=>'1',
            'user_update_id'=>'1',

        ]);
        DB::table('users')->insert([
            'id'=>2,
            'user_name'=>'ktv',
            'full_name'=>'kĩ thuật viên',
            'phone'=>'0961130648',
            'email'=>'ktv@gmail.com',
            'department_id'=>2,
            'role_id'=>2,
            'note'=>'ktv',
            'is_active'=>1,
            'password'=>\Illuminate\Support\Facades\Hash::make(12345678),
            'user_create_id'=>'1',
            'user_update_id'=>'1',


        ]);
        DB::table('users')->insert([
            'id'=>3,
            'user_name'=>'gv',
            'full_name'=>'Giảng viên',
            'phone'=>'0961130648',
            'email'=>'gv@gmail.com',
            'department_id'=>2,
            'role_id'=>0,
            'note'=>'giáo viên',
            'is_active'=>1,
            'password'=>\Illuminate\Support\Facades\Hash::make(12345678),
            'user_create_id'=>'1',
            'user_update_id'=>'1',
        ]);
    }
}
