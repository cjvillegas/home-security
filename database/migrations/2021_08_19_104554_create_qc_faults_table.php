<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQcFaultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qc_faults', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quality_control_id');
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('process_id');
            $table->unsignedBigInteger('scanner_id');
            $table->text('description')->nullable()->default(null);
            $table->dateTime('operation_date')->nullable()->default(null);
            $table->dateTime('created_at')->nullable()->default(null);
            $table->dateTime('updated_at')->nullable()->default(null);
            $table->dateTime('deleted_at')->nullable()->default(null);

            $table->foreign('quality_control_id')->references('id')->on('quality_controls')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('process_id')->references('id')->on('processes')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('scanner_id')->references('id')->on('scanners')->onUpdate('cascade')->onDelete('cascade');

            $table->index('operation_date');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('qc_faults');
    }
}
