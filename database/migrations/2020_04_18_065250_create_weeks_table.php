<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWeeksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     *
     * Khởi tạo bảng tuần
     */
    public function up()
    {
        Schema::create('weeks', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('semesters_id');
            $table->integer('week')->comment('tuần');
            $table->date('start_day')->comment('ngày bắt đầu');
            $table->date('end_day')->comment('ngày kết thúc');
            $table->text('note')->nullable()->comment('ghi chú');
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
        Schema::dropIfExists('weeks');
    }
}
