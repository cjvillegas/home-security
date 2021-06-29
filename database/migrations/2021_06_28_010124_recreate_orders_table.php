<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RecreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('blind_id')->nullable();
            $table->unsignedBigInteger('order_no')->nullable();
            $table->string('customer')->nullable();
            $table->string('customer_order_no')->nullable();
            $table->unsignedInteger('quantity')->nullable();
            $table->string('blind_type')->nullable();
            $table->string('blind_status')->nullable();
            $table->unsignedBigInteger('order_entered_by')->nullable();
            $table->unsignedInteger('serial_id')->nullable();
            $table->dateTime('despatched_at')->nullable();
            $table->dateTime('ordered_at')->nullable();
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
        Schema::dropIfExists('orders');
    }
}
