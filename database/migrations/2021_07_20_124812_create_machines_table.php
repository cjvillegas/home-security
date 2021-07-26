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
            $table->string('parmeter_1')->nullable();
            $table->string('parmeter_2')->nullable();
            $table->string('parmeter_3')->nullable();
            $table->string('parmeter_4')->nullable();
            $table->string('parmeter_5')->nullable();
            $table->string('parmeter_6')->nullable();
            $table->string('parmeter_7')->nullable();
            $table->string('parmeter_8')->nullable();
            $table->string('parmeter_9')->nullable();
            $table->string('parmeter_10')->nullable();
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
