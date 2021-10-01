<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMachineIdColumnInScannersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('scanners', function (Blueprint $table) {
            $table->unsignedBigInteger('machine_id')->nullable()->after('id');

            $table->foreign('machine_id')->references('id')->on('machines')->onUpdate('SET NULL')->onDelete('SET NULL');
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
            $table->dropForeign('scanners_machine_id_foreign');
            $table->dropColumn('machine_id');
        });
    }
}
