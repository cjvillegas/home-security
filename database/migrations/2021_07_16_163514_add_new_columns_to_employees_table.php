<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnsToEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->float('standard_working_hours', 5, 2)->after('target')->default(0);
            $table->integer('clock_num')->after('standard_working_hours')->nullable()->default(null);

            $table->unique('clock_num', 'employees_clock_num_unique');
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

        Schema::table('employees', function (Blueprint $table) use ($sm) {
            $doctrineTable = $sm->listTableDetails('employees');
            // verify if the index exists then do drop index
            if ($doctrineTable->hasIndex('employees_clock_num_index')) {
                $table->dropIndex('employees_clock_num_index');
            }

            $table->dropColumn('standard_working_hours');
            $table->dropColumn('clock_num');
        });
    }
}
