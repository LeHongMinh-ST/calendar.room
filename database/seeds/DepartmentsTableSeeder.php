<?php

use Illuminate\Database\Seeder;

class DepartmentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('departments')->truncate();

            DB::table('departments')->insert([
                'department_id' => 'CNPM',
                'name' => 'Công Nghê Phần mềm',
                'is_active'=>'1',
                'user_create_id'=>'1',
                'user_update_id'=>'1',

            ]);
        DB::table('departments')->insert([
            'department_id' => 'TTUD',
            'name' => 'Toán tin ứng dụng',
            'is_active'=>'1',
            'user_create_id'=>'1',
            'user_update_id'=>'1',

        ]);
        DB::table('departments')->insert([
            'department_id' => 'CNTT',
            'name' => 'Công nghệ thông tin',
            'is_active'=>'1',
            'user_create_id'=>'1',
            'user_update_id'=>'1',
        ]);

        DB::table('departments')->insert([
            'department_id' => 'QT',
            'name' => 'Quản trị',
            'is_active'=>'1',
            'user_create_id'=>'1',
            'user_update_id'=>'1',
        ]);

        DB::table('departments')->insert([
            'department_id' => 'TKT',
            'name' => 'Tổ kĩ thuật',
            'is_active'=>'1',
            'user_create_id'=>'1',
            'user_update_id'=>'1',
        ]);
    }
}
