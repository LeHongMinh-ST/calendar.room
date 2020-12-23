<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('user_name')->comment('tên tài khoản');
            $table->string('email')->nullable()->comment('Địa chỉ email');
            $table->string('password')->comment('Mật khẩu');
            $table->string('full_name')->comment('họ và tên');
            $table->string('phone')->nullable()->comment('Số điện thoại');
            $table->bigInteger('role_id')->comment('Mã phân quyền');
            $table->bigInteger('department_id')->nullable()->comment('Mã bộ môn');
            $table->text('note')->nullable()->comment('gi chú');
            $table->rememberToken();
            $table->timestamps();
            $table->index(['user_name', 'department_id', 'role_id']);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
