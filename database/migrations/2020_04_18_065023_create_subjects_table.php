<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     *
     * Khởi tạo bảng môn học
     */
    public function up()
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->char('subject_id', 10)->nullable()->comment('mã môn học');
            $table->string('name')->nullable()->comment('tên môn học');
            $table->bigInteger('department_id')->nullable();
            $table->tinyInteger('is_active')->default(1)->comment('Trạng thái');
            $table->timestamps();
            $table->softDeletes();
            $table->index('subject_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subjects');
    }
}
