<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('machines', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('serial_no');
            $table->string('location');
            $table->string('supplier')->nullable();
            $table->string('model')->nullable();
            $table->string('parameter_1')->nullable();
            $table->string('parameter_2')->nullable();
            $table->string('parameter_3')->nullable();
            $table->string('parameter_4')->nullable();
            $table->string('parameter_5')->nullable();
            $table->string('parameter_6')->nullable();
            $table->string('parameter_7')->nullable();
            $table->string('parameter_8')->nullable();
            $table->string('parameter_9')->nullable();
            $table->string('parameter_10')->nullable();
            $table->tinyInteger('status')->default(true)->comment('true 1 -> active . false 0 -> inactive');
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
        Schema::dropIfExists('machines');
    }
}
