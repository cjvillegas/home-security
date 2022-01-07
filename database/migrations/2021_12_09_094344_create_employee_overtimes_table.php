<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeOvertimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_overtimes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('overtime_booking_id');
            $table->string('employee_id');
            $table->boolean('is_approved')->default(false);
            $table->string('department')->nullable();
            $table->string('shift')->nullable();
            $table->unsignedBigInteger('checked_by')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->dateTime('rejected_at')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->dateTime('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_overtimes');
    }
}
