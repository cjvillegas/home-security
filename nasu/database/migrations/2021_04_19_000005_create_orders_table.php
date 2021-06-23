<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('blindid')->nullable();
            $table->string('order_no')->nullable();
            $table->string('customer')->nullable();
            $table->string('cust_ord_ref')->nullable();
            $table->string('cust_ord_no')->nullable();
            $table->integer('quantity')->nullable();
            $table->string('blind_type')->nullable();
            $table->string('range')->nullable();
            $table->string('colour')->nullable();
            $table->string('stock_code')->nullable();
            $table->string('man_width')->nullable();
            $table->string('man_drop')->nullable();
            $table->string('blind_status')->nullable();
            $table->date('despatch_date')->nullable();
            $table->date('ordered')->nullable();
            $table->date('required')->nullable();
            $table->date('scheduled_date')->nullable();
            $table->string('roller_table')->nullable();
            $table->string('remake')->nullable();
            $table->string('same_day_despatch')->nullable();
            $table->string('over_size')->nullable();
            $table->string('man_location')->nullable();
            $table->string('order_entered_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
