<?php

namespace App\Models\StockOrder;

use App\Models\SbgModel;
use App\Models\StockLevel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOrderItem extends SbgModel
{
    /**
     * The order statuses
     */
    const STATUS_DRAFT = 0;
    const STATUS_PENDING = 1;
    const STATUS_APPROVED = 2;
    const STATUS_CANCELLED = 3;

    /**
     * List of order statuses
     */
    const ORDER_STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_PENDING,
        self::STATUS_APPROVED,
        self::STATUS_CANCELLED
    ];

    /**
     * Define the mass assignable fields.
     * This will also serve as a list of all the fields
     * handled by this model's table.
     *
     * @var string[]
     */
    protected $fillable = [
        'stock_order_id',
        'stock_level_id',
        'order_qty',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    /**
     * Updates the specified relationship's updated_at column.
     *
     * @var array
     */
    protected $touches = ['order'];

    /**
     * Appends property as part of the default data in each and every retrieved model.
     *
     * @var array
     */
    protected $appends = [
        'order_no'
    ];

    /************************************
     * C U S T O M  P R O P E R T I E S *
     ***********************************/

    /**
     * Generate the order number. The data will come from the ID of the order
     * form the database, then we will pad it with zero at the left side.
     *
     * @return string
     */
    public function getOrderNoAttribute(): string
    {
        return str_pad($this->id, 10, '0', STR_PAD_LEFT) .  'INT';
    }

    /*******************************************
     * E N D  C U S T O M  P R O P E R T I E S *
     ******************************************/

    /********************
     * R E L A T I O N S *
     ********************/

    /**
     * Get the parent order
     *
     * @return BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(StockOrder::class);
    }

    /**
     * @return BelongsTo
     */
    public function stockLevel(): BelongsTo
    {
        return $this->belongsTo(StockLevel::class);
    }

    /********************************
     * E N D  O F  R E L A T I O N S *
     ********************************/
}
