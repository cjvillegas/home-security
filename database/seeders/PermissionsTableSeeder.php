<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            [
                'id'    => 1,
                'title' => 'user_management_access',
            ],
            [
                'id'    => 2,
                'title' => 'permission_create',
            ],
            [
                'id'    => 3,
                'title' => 'permission_edit',
            ],
            [
                'id'    => 4,
                'title' => 'permission_show',
            ],
            [
                'id'    => 5,
                'title' => 'permission_delete',
            ],
            [
                'id'    => 6,
                'title' => 'permission_access',
            ],
            [
                'id'    => 7,
                'title' => 'role_create',
            ],
            [
                'id'    => 8,
                'title' => 'role_edit',
            ],
            [
                'id'    => 9,
                'title' => 'role_show',
            ],
            [
                'id'    => 10,
                'title' => 'role_delete',
            ],
            [
                'id'    => 11,
                'title' => 'role_access',
            ],
            [
                'id'    => 12,
                'title' => 'user_create',
            ],
            [
                'id'    => 13,
                'title' => 'user_edit',
            ],
            [
                'id'    => 14,
                'title' => 'user_show',
            ],
            [
                'id'    => 15,
                'title' => 'user_delete',
            ],
            [
                'id'    => 16,
                'title' => 'user_access',
            ],
            [
                'id'    => 17,
                'title' => 'user_alert_create',
            ],
            [
                'id'    => 18,
                'title' => 'user_alert_show',
            ],
            [
                'id'    => 19,
                'title' => 'user_alert_delete',
            ],
            [
                'id'    => 20,
                'title' => 'user_alert_access',
            ],
            [
                'id'    => 21,
                'title' => 'scanner_create',
            ],
            [
                'id'    => 22,
                'title' => 'scanner_edit',
            ],
            [
                'id'    => 23,
                'title' => 'scanner_show',
            ],
            [
                'id'    => 24,
                'title' => 'scanner_delete',
            ],
            [
                'id'    => 25,
                'title' => 'scanner_access',
            ],
            [
                'id'    => 26,
                'title' => 'employee_create',
            ],
            [
                'id'    => 27,
                'title' => 'employee_edit',
            ],
            [
                'id'    => 28,
                'title' => 'employee_show',
            ],
            [
                'id'    => 29,
                'title' => 'employee_delete',
            ],
            [
                'id'    => 30,
                'title' => 'employee_access',
            ],
            [
                'id'    => 31,
                'title' => 'process_create',
            ],
            [
                'id'    => 32,
                'title' => 'process_edit',
            ],
            [
                'id'    => 33,
                'title' => 'process_show',
            ],
            [
                'id'    => 34,
                'title' => 'process_delete',
            ],
            [
                'id'    => 35,
                'title' => 'process_access',
            ],
            [
                'id'    => 36,
                'title' => 'order_create',
            ],
            [
                'id'    => 37,
                'title' => 'order_edit',
            ],
            [
                'id'    => 38,
                'title' => 'order_show',
            ],
            [
                'id'    => 39,
                'title' => 'order_delete',
            ],
            [
                'id'    => 40,
                'title' => 'order_access',
            ],
            [
                'id'    => 41,
                'title' => 'team_create',
            ],
            [
                'id'    => 42,
                'title' => 'team_edit',
            ],
            [
                'id'    => 43,
                'title' => 'team_show',
            ],
            [
                'id'    => 44,
                'title' => 'team_delete',
            ],
            [
                'id'    => 45,
                'title' => 'team_access',
            ],
            [
                'id'    => 46,
                'title' => 'shift_create',
            ],
            [
                'id'    => 47,
                'title' => 'shift_edit',
            ],
            [
                'id'    => 48,
                'title' => 'shift_show',
            ],
            [
                'id'    => 49,
                'title' => 'shift_delete',
            ],
            [
                'id'    => 50,
                'title' => 'shift_access',
            ],
            [
                'id'    => 51,
                'title' => 'audit_log_show',
            ],
            [
                'id'    => 52,
                'title' => 'audit_log_access',
            ],
            [
                'id'    => 53,
                'title' => 'order_management_access',
            ],
            [
                'id'    => 54,
                'title' => 'orderhistory_create',
            ],
            [
                'id'    => 55,
                'title' => 'orderhistory_edit',
            ],
            [
                'id'    => 56,
                'title' => 'orderhistory_show',
            ],
            [
                'id'    => 57,
                'title' => 'orderhistory_delete',
            ],
            [
                'id'    => 58,
                'title' => 'orderhistory_access',
            ],
            [
                'id'    => 59,
                'title' => 'profile_password_edit',
            ],
            [
                'id' => 60,
                'title' => 'machine_access'
            ],
            [
                'id' => 61,
                'title' => 'machine_create'
            ],
            [
                'id' => 62,
                'title' => 'machine_update'
            ],
            [
                'id' => 63,
                'title' => 'machine_delete'
            ],
            [
                'id' => 64,
                'title' => 'machine_counter_access'
            ],
            [
                'id' => 65,
                'title' => 'machine_counter_create'
            ],
            [
                'id' => 66,
                'title' => 'machine_counter_update'
            ],
            [
                'id' => 66,
                'title' => 'machinecounters_delete'
            ],
        ];

        Permission::insert($permissions);
    }
}
