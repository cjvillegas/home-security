<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_items', function (Blueprint $table) {
            $table->id();
            $table->string('stock_code');
            $table->text('description')->nullable();
            $table->string('range')->nullable();
            $table->string('colour')->nullable();
            $table->string('size')->nullable();
            $table->string('length')->nullable();
            $table->string('product_picture')->nullable();
            $table->string('main_location')->nullable();
            $table->string('main_location_picture')->nullable();
            $table->string('secondary_location')->nullable();
            $table->string('secondary_location_picture')->nullable();
            $table->string('other_location')->nullable();
            $table->string('other_location_picture')->nullable();
            $table->string('alt_item_code')->nullable();
            $table->string('parameter1')->nullable();
            $table->string('parameter2')->nullable();
            $table->string('parameter3')->nullable();
            $table->string('parameter4')->nullable();
            $table->string('parameter5')->nullable();
            $table->boolean('status')->default(false);
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
        Schema::dropIfExists('stock_items');
    }
}
