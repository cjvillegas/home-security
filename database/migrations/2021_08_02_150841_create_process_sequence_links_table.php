<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcessSequenceLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('process_sequence_links', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('process_sequence_id');
            $table->unsignedBigInteger('process_id');
            $table->unsignedBigInteger('previous_step_id')->nullable()->default(null);
            $table->unsignedBigInteger('next_step_id')->nullable()->default(null);
            $table->integer('order');
            $table->dateTime('created_at')->nullable();

            $table->foreign('process_sequence_id')->references('id')->on('process_sequences')->onDelete('CASCADE');
            $table->foreign('process_id')->references('id')->on('processes')->onDelete('CASCADE');
            $table->foreign('previous_step_id')->references('id')->on('process_sequence_links')->onDelete('SET NULL');
            $table->foreign('next_step_id')->references('id')->on('process_sequence_links')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('process_sequence_links');
    }
}
