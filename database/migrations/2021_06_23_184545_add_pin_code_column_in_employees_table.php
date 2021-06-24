<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use \App\Models\Employee;

class AddPinCodeColumnInEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('pin_code')->after('barcode');
            $table->index('pin_code', 'employee_pin_code_index');
        });

        // after creating the pin_code column, populate its value with the barcode column
        // this is useful so that column will not be empty
        foreach (Employee::get() as $employee) {
            $employee->pin_code = $employee->barcode;
            $employee->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('pin_code');

            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $doctrineTable = $sm->listTableDetails('employees');
            // verify if the index exists then do drop index
            if ($doctrineTable->hasIndex('employee_pin_code_index')) {
                $table->dropIndex('employee_pin_code_index');
            }
        });
    }
}
