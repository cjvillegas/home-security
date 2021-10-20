<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAndAlternateColumnInProcessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('processes', function (Blueprint $table) {
            $table->integer('internet_target')->nullable()->after('new_joiner_target');
            $table->integer('internet_target_new_joiner')->nullable()->after('internet_target');

            $table->renameColumn('process_target', 'trade_target');
            $table->renameColumn('new_joiner_target', 'trade_target_new_joiner');
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
            $table->dropColumn('internet_target');
            $table->dropColumn('internet_target_new_joiner');

            $table->renameColumn('trade_target', 'process_target');
            $table->renameColumn('trade_target_new_joiner', 'new_joiner_target');
        });
    }
}
