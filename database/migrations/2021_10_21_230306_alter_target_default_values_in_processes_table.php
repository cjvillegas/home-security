<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTargetDefaultValuesInProcessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('processes', function (Blueprint $table) {
            $table->integer('trade_target')->default(0)->change();
            $table->integer('internet_target')->default(0)->change();
            $table->integer('trade_target_new_joiner')->default(0)->change();
            $table->integer('internet_target_new_joiner')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('processes', function (Blueprint $table) {
            $table->integer('trade_target')->nullable()->change();
            $table->integer('internet_target')->nullable()->change();
            $table->integer('trade_target_new_joiner')->nullable()->change();
            $table->integer('internet_target_new_joiner')->nullable()->change();
        });
    }
}
