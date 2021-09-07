<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShiftAssignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shift_assignments', function (Blueprint $table) {
            $table->id();
            $table->integer('serial_id');
            $table->string('folder_name');
            $table->dateTime('scheduled_date');
            $table->dateTime('work_date');

            $table->index('serial_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shift_assignments');
    }
}
