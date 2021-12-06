<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class TableColumnManager extends SbgModel
{
    use HasFactory;

    /**
     * Used in order listing
     */
    const TYPE_ORDER = 1;

    /**
     * List all filter types
     */
    const COLUMN_TYPES = [
        self::TYPE_ORDER
    ];

    /**
     * Protected fields
     *
     * @var array
     */
    protected $guarded = [
        'user_id',
        'type',
        'columns',
        'created_at',
        'updated_at',
    ];

    /**
     * Cast variables to specified data types
     *
     * @var array
     */
    protected $casts = [
        'columns' => 'array',
    ];
}
