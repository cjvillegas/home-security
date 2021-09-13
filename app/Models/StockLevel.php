<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockLevel extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'code',
        'name',
        'available_stock',
        'po_stock',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /********************
    * R E L A T I O N S *
    ********************/

    /**
     * This will return Purchase Orders
     *
     * @return HasMany
     */
    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'code', 'code');
    }

    /********************************
    * E N D  O F  R E L A T I O N S *
    ********************************/
}
