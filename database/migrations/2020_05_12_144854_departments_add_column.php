<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DepartmentsAddColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->bigInteger('user_create_id')->after('name');
            $table->bigInteger('user_update_id')->after('user_create_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn('user_create_id');
            $table->dropColumn('user_update_id');
        });
    }
}
