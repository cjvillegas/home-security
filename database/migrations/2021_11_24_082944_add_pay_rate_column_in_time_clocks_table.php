<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPayRateColumnInTimeClocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('time_clocks', function (Blueprint $table) {
            $table->float('pay_rate')->nullable()->after('clock_num');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('time_clocks', function (Blueprint $table) {
            $table->dropColumn('pay_rate');
        });
    }
}
