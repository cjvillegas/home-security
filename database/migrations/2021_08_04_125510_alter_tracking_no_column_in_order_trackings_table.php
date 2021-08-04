<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTrackingNoColumnInOrderTrackingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_trackings', function (Blueprint $table) {
            $table->string('tracking_no')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_trackings', function (Blueprint $table) {
            $table->unsignedBigInteger('tracking_no')->nullable()->change();
        });
    }
}
