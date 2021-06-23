<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToOrderhistoriesTable extends Migration
{
    public function up()
    {
        Schema::table('orderhistories', function (Blueprint $table) {
            $table->unsignedBigInteger('order_number_id')->nullable();
            $table->foreign('order_number_id', 'order_number_fk_3717079')->references('id')->on('orders');
        });
    }
}
