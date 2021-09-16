<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexToFolderNameColumnInShiftAssignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shift_assignments', function (Blueprint $table) {
            $table->index('folder_name', 'shift_assignments_folder_name_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shift_assignments', function (Blueprint $table) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();

            $doctrineTable = $sm->listTableDetails('shift_assignments');
            // verify if the index exists then do drop index
            if ($doctrineTable->hasIndex('shift_assignments_folder_name_index')) {
                $table->dropIndex('shift_assignments_folder_name_index');
            }
        });
    }
}
