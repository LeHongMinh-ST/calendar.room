<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHandleFeedbacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     *
     * Khởi tạo bảng xử lý phản ánh
     */
    public function up()
    {
        Schema::create('handle_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('feedback_id');
            $table->bigInteger('user_id');
//            $table->string('people_confirm')->comment('người xác nhận');
            $table->timestamp('date_confirm')->comment('ngày xác nhận');
            $table->text('description')->comment('mô tả');
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
        Schema::dropIfExists('handle_feedbacks');
    }
}
