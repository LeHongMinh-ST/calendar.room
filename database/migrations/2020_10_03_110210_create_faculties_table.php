<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacultiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('faculties', function (Blueprint $table) {
            $table->id();
            $table->char('faculty_id', 10)->comment('mã khoa');
            $table->string('name')->comment('tên khoa');
            $table->tinyInteger('is_active')->default(1)->comment('Trạng thái');
            $table->bigInteger('user_create_id')->nullable()->comment('id người tạo');
            $table->bigInteger('user_update_id')->nullable()->comment('id người cập nhật');;
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
        Schema::dropIfExists('faculties');
    }
}
