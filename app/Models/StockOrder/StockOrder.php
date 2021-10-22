<?php

namespace App\Models\StockOrder;

use Illuminate\Database\Eloquent\Model;

class StockOrder extends Model
{
    /**
     * The order statuses
     */
    const STATUS_DRAFT = 0;
    const STATUS_NEW = 1;
    const STATUS_PENDING = 2;

    /**
     * Define the mass assignable fields.
     * This will also serve as a list of all the fields
     * handled by this model's table.
     *
     * @var string[]
     */
    protected $fillable = [
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}
