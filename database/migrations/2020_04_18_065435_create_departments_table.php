<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     *
     * Khởi tạo bảng bộ môn
     */
    public function up()
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->char('department_id', 10)->unique()->comment('mã bộ môn');
            $table->string('name')->comment('tên bộ môn');
            $table->tinyInteger('is_active')->default(1)->comment('Trạng thái');
            $table->timestamps();
            $table->softDeletes();
            $table->index('department_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('departments');
    }
}
