<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_order_id');
            $table->unsignedBigInteger('stock_level_id');
            $table->integer('order_qty');
            $table->unsignedTinyInteger('status');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->dateTime('created_at');
            $table->dateTime('updated_at')->nullable();

            $table->foreign('stock_order_id')->references('id')->on('stock_orders')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('stock_level_id')->references('id')->on('stock_levels')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('created_by')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');
            $table->foreign('updated_by')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('NO ACTION');

            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_order_items');
    }
}
