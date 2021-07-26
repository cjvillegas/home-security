<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBarcodeColumnInUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('barcode')->nullable()->default(null)->after('email')->comment('This is used for employees. We need to have them logged in using their barcodes instead of email.');

            $table->unique('barcode', 'users_barcode_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $sm = Schema::getConnection()->getDoctrineSchemaManager();

        Schema::table('users', function (Blueprint $table) use ($sm) {
            $doctrineTable = $sm->listTableDetails('users');
            // verify if the index exists then do drop index
            if ($doctrineTable->hasIndex('users_barcode_unique')) {
                $table->dropIndex('users_barcode_unique');
            }

            $table->dropColumn('barcode');
        });
    }
}
