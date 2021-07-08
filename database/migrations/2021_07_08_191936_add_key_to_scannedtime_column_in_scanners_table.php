<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKeyToScannedtimeColumnInScannersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('scanners', function (Blueprint $table) {
            $table->index('scannedtime', 'scanners_scannedtime_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('scanners', function (Blueprint $table) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $doctrineTable = $sm->listTableDetails('scanners');
            // verify if the index exists then do drop index
            if ($doctrineTable->hasIndex('scanners_scannedtime_index')) {
                $table->dropIndex('scanners_scannedtime_index');
            }
        });
    }
}
