<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AssignmentsAddColumnUserCreateId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->bigInteger('user_create_id')->nullable()->after('execution_time')->comment('id người tạo');
            $table->bigInteger('user_update_id')->nullable()->after('user_create_id')->comment('id người cập nhật');;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assignments', function (Blueprint $table) {
                $table->dropColumn('user_create_id');
                $table->dropColumn('user_update_id');
        });
    }
}
