<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     *
     * Khởi tạo bảng phòng máy
     */
    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->char('room_id', 10)->nullable()->comment('mã phòng máy');
            $table->string('name')->nullable()->comment('tên phòng');
            $table->string('status')->nullable()->comment('tình trạng');
            $table->integer('computer_number')->nullable()->comment('số máy');
            $table->string('address')->nullable()->comment('địa chỉ');
            $table->text('subject')->nullable()->comment('môn học');
            $table->text('software')->nullable()->comment('phần mềm');
            $table->tinyInteger('is_active')->default(1)->comment('Trạng thái 1:hoạt động - 0: ngưng hoạt động');
            $table->timestamps();
            $table->softDeletes();
            $table->index('room_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rooms');
    }
}
