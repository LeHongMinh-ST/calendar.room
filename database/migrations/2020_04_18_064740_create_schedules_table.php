<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     *
     * Khởi tạo bảng thời khóa biểu
     */
    public function up()
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('room_id')->comment('Mã phòng máy');
            $table->bigInteger('semester_id')->comment('Mã học kì');
            $table->bigInteger('subject_id')->comment('Mã môn học');
            $table->integer('subject_group')->comment('nhóm môn học');
            $table->char('teacher_id', 10)->comment('mã giảng viên');
            $table->char('class',10)->comment('mã lớp');
            $table->integer('amount_people')->comment('sĩ số');
            $table->integer('day')->comment('thứ');
            $table->integer('session')->comment('tiết học bắt đầu');
            $table->integer('number_session')->comment('số tiết');
            $table->integer('week')->comment('tuần bắt đầu');
            $table->integer('number_week')->comment('số tuần');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['room_id', 'semester_id', 'subject_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schedules');
    }
}
