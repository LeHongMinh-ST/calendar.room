<?php

use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->truncate();

        DB::table('roles')->insert([
            'role_id' => 0,
            'user_groups' => 'Giảng viên',
            'description' => 'Nhóm người dùng là Giảng viên: có quyền đổi mật khẩu, cập nhật email, số điện thoại cá nhân; xem lịch phòng máy, xem tình trạng phòng máy(bình thường, đang bảo trì); đăng ký sử dụng phòng máy; xem/gửi phản ánh về tình trạng máy tính.',
            'user_create_id' => '1',
            'user_update_id' => '1',
        ]);

        DB::table('roles')->insert([
            'role_id' => 1,
            'user_groups' => 'Quản trị',
            'description' => '	Người dùng là Người quản lý: có quyền truy cập tất cả các chức năng của hệ thống, đặc biệt có quyền sao lưu dữ liệu, phục hồi dữ liệu, quản lý người dùng và xác nhận yêu cầu đăng ký lịch sử dụng phòng máy của giảng viên.',
            'user_create_id' => '1',
            'user_update_id' => '1',
        ]);

        DB::table('roles')->insert([
            'role_id' => 2,
            'user_groups' => 'Kĩ thuật',
            'description' => '	Nhóm người dùng là Kỹ thuật viên: có quyền đổi mật khẩu, cập nhật email, số điện thoại cá nhân; xem/xác nhận đã xử lý phản ánh của giảng viên về tình trạng máy tính; cập nhật thông tin và tình trạng phòng máy.',
            'user_create_id' => '1',
            'user_update_id' => '1',
        ]);
    }
}
