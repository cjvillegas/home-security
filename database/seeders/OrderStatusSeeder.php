<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // removes key checks, it will cause an error during table reset
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');

        DB::table('order_statuses')->delete();

        // reset auto increment to zero
        DB::statement('ALTER TABLE order_statuses AUTO_INCREMENT = 1;');

        $now = now();

        DB::table('order_statuses')
            ->insert(array (
                [
                    'name' => 'Not Started',
                    'created_at' => $now
                ],
                [
                    'name' => 'Partially Manufactured',
                    'created_at' => $now
                ],
                [
                    'name' => 'Partially Packed',
                    'created_at' => $now
                ],
                [
                    'name' => 'Fully Manufactured',
                    'created_at' => $now
                ],
                [
                    'name' => 'Order Packed',
                    'created_at' => $now
                ],
                [
                    'name' => 'Order Shipped',
                    'created_at' => $now
                ]
            )
        );

        // enable key checks again
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
