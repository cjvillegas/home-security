<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    /**
     * Mass assignable fields
     *
     * @var string[]
     */
    protected $fillable = [
        'code',
        'order_date',
        'estimated_delivery',
        'quantity',
        'date_time_updated',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Get most recent Date record in Purchase Orders Table
     *
     * @return string
     */
    public function getMostRecentUpdateDate(): ?string
    {
        $latest = self::latest('date_time_updated')->first();

        return optional($latest)->date_time_updated;
    }
}
