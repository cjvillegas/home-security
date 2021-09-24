<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderInvoice extends Model
{
    use SoftDeletes;

    /**
     * Mass assignable fields
     *
     * @var array
     */
    protected $fillable = [
        'order_no',
        'invoice_no',
        'date',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}
