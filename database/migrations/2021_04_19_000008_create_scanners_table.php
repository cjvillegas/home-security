<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScannersTable extends Migration
{
    public function up()
    {
        Schema::create('scanners', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->datetime('scannedtime')->nullable();
            $table->string('employeeid')->nullable();
            $table->string('processid')->nullable();
            $table->string('blindid')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
