<?php

namespace App\Models;

class Filter extends SbgModel
{
    /**
     * Used in order listing
     */
    const TYPE_ORDER = 1;

    /**
     * List all filter types
     */
    const FILTER_TYPES = [
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
        'name',
        'filters',
        'created_at',
        'updated_at',
    ];

    /**
     * Cast variables to specified data types
     *
     * @var array
     */
    protected $casts = [
        'filters' => 'array',
    ];
}
