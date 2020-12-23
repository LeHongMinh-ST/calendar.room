<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSemestersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     *
     * Khởi tạo bảng học kỳ
     */
    public function up()
    {
        Schema::create('semesters', function (Blueprint $table) {
            $table->id();
            $table->integer('semester')->comment('học kỳ');
            $table->char('school_year', 20)->comment('năm học');
            $table->integer('number_weeks')->comment('số tuần');
            $table->date('semester_start_date')->comment('ngày bắt đầu học kỳ');
            $table->date('semester_end_date')->comment('ngày kết thúc học kỳ');
            $table->timestamps();
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
        Schema::dropIfExists('semesters');
    }
}
