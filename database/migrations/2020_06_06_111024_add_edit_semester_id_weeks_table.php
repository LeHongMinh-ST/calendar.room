<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEditSemesterIdWeeksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('weeks', function (Blueprint $table) {
            if (Schema::hasColumn('weeks', 'semesters_id')) {
                $table->renameColumn('semesters_id', 'semester_id');
            }

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('weeks', function (Blueprint $table) {
            if (Schema::hasColumn('weeks', 'semester_id')) {
                $table->renameColumn('semester_id', 'semesters_id');
            }
        });
    }
}
