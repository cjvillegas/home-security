<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQcRemakeValidatedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qc_remake_validateds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('qc_remake_id');
            $table->string('blind_id');
            $table->string('barcode')->unique();
            $table->string('question_key')->nullable();
            $table->text('reason')->nullable();
            $table->boolean('is_fully_verified')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            $table->foreign('qc_remake_id')->references('id')->on('qc_remakes')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('qc_remake_validateds');
    }
}
