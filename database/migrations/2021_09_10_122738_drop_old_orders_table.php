<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DropOldOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        Schema::dropIfExists('old_orders');

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        Schema::create('old_orders', function (Blueprint $table) {
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

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
