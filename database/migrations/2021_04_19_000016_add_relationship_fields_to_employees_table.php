<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToEmployeesTable extends Migration
{
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->unsignedBigInteger('shift_id');
            $table->foreign('shift_id', 'shift_fk_3697202')->references('id')->on('shifts');
            $table->unsignedBigInteger('team_id');
            $table->foreign('team_id', 'team_fk_3697203')->references('id')->on('teams');
        });
    }
}
