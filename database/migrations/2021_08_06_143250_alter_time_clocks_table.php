<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTimeClocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('time_clocks', function (Blueprint $table) {
            $table->unsignedBigInteger('employee_id')->nullable()->default(null)->change();
            $table->unsignedBigInteger('trans_id')->nullable()->default(null)->after('employee_id')->comment('The ID counterpart in the t&a table.');

            $table->renameColumn('clock_in', 'swiped_at');

            $table->dropColumn('clock_out');
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
            $table->unsignedBigInteger('employee_id')->change();

            $table->renameColumn('swiped_at', 'clock_in');

            $table->dateTime('clock_out')->nullable()->after('swiped_at');

            // drop if exist
            if (Schema::hasColumn('time_clocks', 'trans_id')) {
                $table->dropColumn('trans_id');
            }
        });
    }
}
