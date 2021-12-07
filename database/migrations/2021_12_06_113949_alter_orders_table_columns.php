<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterOrdersTableColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('width')->nullable()->after('account_code')->change();
            $table->integer('drop')->nullable()->after('width')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('width', 10, 2)->nullable()->after('account_code')->change();
            $table->decimal('drop', 10, 2)->nullable()->after('width')->change();
        });
    }
}
