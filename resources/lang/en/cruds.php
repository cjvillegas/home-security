<?php

return [
    'userManagement' => [
        'title'          => 'User management',
        'title_singular' => 'User management',
    ],
    'permission' => [
        'title'          => 'Permissions',
        'title_singular' => 'Permission',
        'fields'         => [
            'id'                => 'ID',
            'id_helper'         => ' ',
            'title'             => 'Title',
            'title_helper'      => ' ',
            'created_at'        => 'Created at',
            'created_at_helper' => ' ',
            'updated_at'        => 'Updated at',
            'updated_at_helper' => ' ',
            'deleted_at'        => 'Deleted at',
            'deleted_at_helper' => ' ',
        ],
    ],
    'role' => [
        'title'          => 'Roles',
        'title_singular' => 'Role',
        'fields'         => [
            'id'                 => 'ID',
            'id_helper'          => ' ',
            'title'              => 'Title',
            'title_helper'       => ' ',
            'permissions'        => 'Permissions',
            'permissions_helper' => ' ',
            'created_at'         => 'Created at',
            'created_at_helper'  => ' ',
            'updated_at'         => 'Updated at',
            'updated_at_helper'  => ' ',
            'deleted_at'         => 'Deleted at',
            'deleted_at_helper'  => ' ',
        ],
    ],
    'user' => [
        'title'          => 'Users',
        'title_singular' => 'User',
        'fields'         => [
            'id'                       => 'ID',
            'id_helper'                => ' ',
            'name'                     => 'Name',
            'name_helper'              => ' ',
            'email'                    => 'Email',
            'email_helper'             => ' ',
            'email_verified_at'        => 'Email verified at',
            'email_verified_at_helper' => ' ',
            'password'                 => 'Password',
            'password_helper'          => ' ',
            'roles'                    => 'Roles',
            'roles_helper'             => ' ',
            'remember_token'           => 'Remember Token',
            'remember_token_helper'    => ' ',
            'created_at'               => 'Created at',
            'created_at_helper'        => ' ',
            'updated_at'               => 'Updated at',
            'updated_at_helper'        => ' ',
            'deleted_at'               => 'Deleted at',
            'deleted_at_helper'        => ' ',
        ],
    ],
    'userAlert' => [
        'title'          => 'User Alerts',
        'title_singular' => 'User Alert',
        'fields'         => [
            'id'                => 'ID',
            'id_helper'         => ' ',
            'alert_text'        => 'Alert Text',
            'alert_text_helper' => ' ',
            'alert_link'        => 'Alert Link',
            'alert_link_helper' => ' ',
            'user'              => 'Users',
            'user_helper'       => ' ',
            'created_at'        => 'Created at',
            'created_at_helper' => ' ',
            'updated_at'        => 'Updated at',
            'updated_at_helper' => ' ',
        ],
    ],
    'scanner' => [
        'title'          => 'Scanned Data',
        'title_singular' => 'Scanned Data',
        'fields'         => [
            'id'                 => 'ID',
            'id_helper'          => ' ',
            'scannedtime'        => 'Scannedtime',
            'scannedtime_helper' => ' ',
            'created_at'         => 'Created at',
            'created_at_helper'  => ' ',
            'updated_at'         => 'Updated at',
            'updated_at_helper'  => ' ',
            'deleted_at'         => 'Deleted at',
            'deleted_at_helper'  => ' ',
            'employeeid'         => 'Employeeid',
            'employeeid_helper'  => ' ',
            'processid'          => 'Processid',
            'processid_helper'   => ' ',
            'blindid'            => 'Blindid',
            'blindid_helper'     => ' ',
        ],
    ],
    'employee' => [
        'title'          => 'Employees',
        'title_singular' => 'Employee',
        'fields'         => [
            'id'                => 'ID',
            'id_helper'         => ' ',
            'fullname'          => 'Full Name',
            'fullname_helper'   => ' ',
            'barcode'           => 'Employee Barcode',
            'pin_code'          => 'Employee Pin Code',
            'barcode_helper'    => ' ',
            'created_at'        => 'Created at',
            'created_at_helper' => ' ',
            'updated_at'        => 'Updated at',
            'updated_at_helper' => ' ',
            'deleted_at'        => 'Deleted at',
            'deleted_at_helper' => ' ',
            'target'            => 'Shift Target',
            'target_helper'     => 'Target per shift',
            'shift'             => 'Shift',
            'shift_helper'      => ' ',
            'team'              => 'Team',
            'team_helper'       => ' ',
        ],
    ],
    'process' => [
        'title'          => 'Processes',
        'title_singular' => 'Process',
        'fields'         => [
            'id'                => 'ID',
            'id_helper'         => ' ',
            'name'              => 'Process Name',
            'name_helper'       => 'Manufacturing Process name',
            'barcode'           => 'Process Barcode',
            'barcode_helper'    => 'The process barcode should always start with E in front of the number ex: E123456',
            'created_at'        => 'Created at',
            'created_at_helper' => ' ',
            'updated_at'        => 'Updated at',
            'updated_at_helper' => ' ',
            'deleted_at'        => 'Deleted at',
            'deleted_at_helper' => ' ',
        ],
    ],
    'order' => [
        'order_list'     => 'Orders',
        'title'          => 'Order Enquiry',
        'title_singular' => 'Order Enquiry',
        'fields'         => [
            'id'                       => 'ID',
            'id_helper'                => ' ',
            'blindid'                  => 'Blindid',
            'blindid_helper'           => ' ',
            'created_at'               => 'Created at',
            'created_at_helper'        => ' ',
            'updated_at'               => 'Updated at',
            'updated_at_helper'        => ' ',
            'deleted_at'               => 'Deleted at',
            'deleted_at_helper'        => ' ',
            'order_no'                 => 'Order No',
            'order_no_helper'          => ' ',
            'customer'                 => 'Customer',
            'customer_helper'          => ' ',
            'cust_ord_ref'             => 'CustOrdRef',
            'cust_ord_ref_helper'      => ' ',
            'cust_ord_no'              => 'CustOrdNo',
            'cust_ord_no_helper'       => ' ',
            'quantity'                 => 'Quantity',
            'quantity_helper'          => ' ',
            'blind_type'               => 'BlindType',
            'blind_type_helper'        => ' ',
            'range'                    => 'Range',
            'range_helper'             => ' ',
            'colour'                   => 'Colour',
            'colour_helper'            => ' ',
            'stock_code'               => 'StockCode',
            'stock_code_helper'        => ' ',
            'man_width'                => 'ManWidth',
            'man_width_helper'         => ' ',
            'man_drop'                 => 'Man Drop',
            'man_drop_helper'          => ' ',
            'blind_status'             => 'Blind Status',
            'blind_status_helper'      => ' ',
            'despatch_date'            => 'Despatch Date',
            'despatch_date_helper'     => ' ',
            'ordered'                  => 'Ordered',
            'ordered_helper'           => ' ',
            'required'                 => 'Required',
            'required_helper'          => ' ',
            'scheduled_date'           => 'Scheduled Date',
            'scheduled_date_helper'    => ' ',
            'roller_table'             => 'Roller Table',
            'roller_table_helper'      => ' ',
            'remake'                   => 'Remake',
            'remake_helper'            => ' ',
            'same_day_despatch'        => 'Same Day Despatch',
            'same_day_despatch_helper' => ' ',
            'over_size'                => 'Over Size',
            'over_size_helper'         => ' ',
            'man_location'             => 'Man Location',
            'man_location_helper'      => ' ',
            'order_entered_by'         => 'OrderEnteredBy',
            'order_entered_by_helper'  => ' ',
        ],
    ],
    'team' => [
        'title'          => 'Teams',
        'title_singular' => 'Team',
        'fields'         => [
            'id'                => 'ID',
            'id_helper'         => ' ',
            'name'              => 'Team Name',
            'name_helper'       => ' ',
            'target'            => 'Team Target',
            'target_helper'     => 'Team daily target',
            'created_at'        => 'Created at',
            'created_at_helper' => ' ',
            'updated_at'        => 'Updated at',
            'updated_at_helper' => ' ',
            'deleted_at'        => 'Deleted at',
            'deleted_at_helper' => ' ',
        ],
    ],
    'shift' => [
        'title'          => 'Shifts',
        'title_singular' => 'Shift',
        'fields'         => [
            'id'                => 'ID',
            'id_helper'         => ' ',
            'name'              => 'Shift Name',
            'name_helper'       => 'Shift Name',
            'created_at'        => 'Created at',
            'created_at_helper' => ' ',
            'updated_at'        => 'Updated at',
            'updated_at_helper' => ' ',
            'deleted_at'        => 'Deleted at',
            'deleted_at_helper' => ' ',
        ],
    ],
    'auditLog' => [
        'title'          => 'Audit Logs',
        'title_singular' => 'Audit Log',
        'fields'         => [
            'id'                  => 'ID',
            'id_helper'           => ' ',
            'description'         => 'Description',
            'description_helper'  => ' ',
            'subject_id'          => 'Subject ID',
            'subject_id_helper'   => ' ',
            'subject_type'        => 'Subject Type',
            'subject_type_helper' => ' ',
            'user_id'             => 'User ID',
            'user_id_helper'      => ' ',
            'properties'          => 'Properties',
            'properties_helper'   => ' ',
            'host'                => 'Host',
            'host_helper'         => ' ',
            'created_at'          => 'Created at',
            'created_at_helper'   => ' ',
            'updated_at'          => 'Updated at',
            'updated_at_helper'   => ' ',
        ],
    ],
    'orderManagement' => [
        'title'          => 'Order Management',
        'title_singular' => 'Order Management',
    ],
    'reports' => [
        'title' => 'Reports',
        'child' => [
            'work_analytics' => [
                'title' => 'Work Analytics'
            ]
        ]
    ],
    'orderhistory' => [
        'title'          => 'Order History',
        'title_singular' => 'Order History',
        'fields'         => [
            'id'                  => 'ID',
            'id_helper'           => ' ',
            'created_at'          => 'Created at',
            'created_at_helper'   => ' ',
            'updated_at'          => 'Updated at',
            'updated_at_helper'   => ' ',
            'deleted_at'          => 'Deleted at',
            'deleted_at_helper'   => ' ',
            'order_number'        => 'Order Number',
            'order_number_helper' => ' ',
        ],
    ],
];
