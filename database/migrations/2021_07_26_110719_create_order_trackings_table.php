<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderTrackingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_trackings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_no')->nullable();
            $table->string('cust_ref')->nullable();
            $table->string('tracking_no')->nullable();
            $table->string('courier')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->dateTime('deleted_at')->nullable();

            $table->index('order_no', 'order_trackings_order_no_index');
            $table->index('cust_ref', 'order_trackings_cust_ref_index');
            $table->index('tracking_no', 'order_trackings_tracking_no_index');
            $table->index('courier', 'order_trackings_courier_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_trackings');
    }
}
