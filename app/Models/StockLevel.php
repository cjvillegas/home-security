<?php

namespace App\Models;

use App\Models\StockOrder\StockOrderItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

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
    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class, 'code', 'code');
    }

    /********************************
    * E N D  O F  R E L A T I O N S *
    ********************************/

    /***************
     * S C O P E S *
     ***************/

    /**
     * Left join to stock_order_items table
     *
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeLeftJoinStockOrderItem(Builder $builder): Builder
    {
        return $builder->leftJoin('stock_order_items AS soi', 'soi.stock_level_id', 'stock_levels.id');
    }

    /**
     * Compute the currently on pending order
     *
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopePendingOrderCount(Builder $builder): Builder
    {
        $pending = StockOrderItem::STATUS_PENDING;

        return $builder->addSelect(DB::raw("CAST(SUM(CASE WHEN soi.id AND soi.status = {$pending} THEN soi.order_qty ELSE 0 END) AS SIGNED) AS pending_order_count"));
    }

    /***************************
     * E N D  O F  S C O P E S *
     ***************************/
}
