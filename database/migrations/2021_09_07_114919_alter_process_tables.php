<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterProcessTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('processes', function (Blueprint $table) {
            $table->float('process_target', 8, 2)->nullable()->default(null)->after('barcode');
            $table->float('new_joiner_target', 8, 2)->nullable()->default(null)->after('process_target');
            $table->float('process_manufacturing_time', 8, 2)->nullable()->default(null)->after('new_joiner_target');
            $table->boolean('stop_start_button_required')->default(true)->after('process_manufacturing_time')->comment('This will indicate if a button to stop or start is required.');
        });

        Schema::table('process_sequences', function (Blueprint $table) {
            $table->dropColumn('process_target');
            $table->dropColumn('new_joiner_target');
            $table->dropColumn('process_manufacturing_time');
            $table->dropColumn('stop_start_button_required');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('processes', function (Blueprint $table) {
            $table->dropColumn('process_target');
            $table->dropColumn('new_joiner_target');
            $table->dropColumn('process_manufacturing_time');
            $table->dropColumn('stop_start_button_required');
        });

        Schema::table('process_sequences', function (Blueprint $table) {
            $table->float('process_target', 8, 2)->nullable()->default(null)->after('name');
            $table->float('new_joiner_target', 8, 2)->nullable()->default(null)->after('process_target');
            $table->float('process_manufacturing_time', 8, 2)->nullable()->default(null)->after('new_joiner_target');
            $table->boolean('stop_start_button_required')->default(true)->after('process_manufacturing_time')->comment('This will indicate if a button to stop or start is required.');
        });
    }
}
