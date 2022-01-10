<?php

namespace App\Models\StockOrder;

use App\Models\SbgModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class StockOrder extends SbgModel
{
    use SoftDeletes;

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
        'status',
        'created_by',
        'updated_by',
        'approved_by',
        'sage_order_no',
        'picking_url',
        'created_at',
        'approved_at',
        'updated_at',
        'deleted_at'
    ];

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
     * Get items of the orders
     *
     * @return HasMany
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(StockOrderItem::class);
    }

    /**
     * Get the user who approved the order
     *
     * @return HasOne
     */
    public function approver(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'approved_by');
    }

    /**
     * Get the user who created the order
     *
     * @return HasOne
     */
    public function creator(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    /********************************
     * E N D  O F  R E L A T I O N S *
     ********************************/

    /***************
     * S C O P E S *
     ***************/

    /**
     * Computed property for status color
     *
     * @param Builder $builder
     */
    public function scopeStatusColor(Builder $builder): Builder
    {
        $caseQuery = $this->queryCaseColumn(
            [
                self::STATUS_DRAFT => '#3e47a7',
                self::STATUS_PENDING => '#f9c710',
                self::STATUS_APPROVED => '#8bcc4c',
                self::STATUS_CANCELLED => '#ee4526',
            ],
            'status',
            'status_color'
        );

        return $builder->addSelect(DB::raw($caseQuery));
    }

    /**
     * Generate a computed property for the status name of the order.
     * This will be the single source of truth for each order status names.
     *
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeStatusName(Builder $builder): Builder
    {
        $caseQuery = $this->queryCaseColumn(
            [
                self::STATUS_DRAFT => 'Draft',
                self::STATUS_PENDING => 'Pending',
                self::STATUS_APPROVED => 'Approved',
                self::STATUS_CANCELLED => 'Cancelled',
            ],
            'status',
            'status_name'
        );

        return $builder->addSelect(DB::raw($caseQuery));
    }

    /**
     * Draft orders only
     *
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeDraft(Builder $builder): Builder
    {
        return $builder->where('stock_orders.status', self::STATUS_DRAFT);
    }

    /**
     * Pending orders only
     *
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopePending(Builder $builder): Builder
    {
        return $builder->where('stock_orders.status', self::STATUS_PENDING);
    }

    /**
     * Approved orders only
     *
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeApproved(Builder $builder): Builder
    {
        return $builder->where('stock_orders.status', self::STATUS_APPROVED);
    }

    /**
     * Add a left join to stock_order_items table
     *
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeLeftJoinStockOrderItem(Builder $builder): Builder
    {
        return $builder->leftJoin('stock_order_items AS soi', 'soi.stock_order_id', 'stock_orders.id');
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeLeftJoinCreator(Builder $builder): Builder
    {
        return $builder->leftJoin('users AS u', 'u.id', 'stock_orders.created_by');
    }

    /**
     * Left join to users using the approved_by column
     *
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeLeftJoinApprover(Builder $builder): Builder
    {
        return $builder->leftJoin('users AS app', 'app.id', 'stock_orders.approved_by');
    }

    /**
     * Compute the count of items in the order. This select statement assumes that the main
     * query already has a join to the stock_order_items table
     *
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeItemsCount(Builder $builder): Builder
    {
        return $builder->addSelect(DB::raw("COUNT(DISTINCT soi.id) AS order_item_count"));
    }

    /**
     * Get the user who created the order. This select statement assumes that the main
     * query already has a join to the users table
     *
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeCreator(Builder $builder): Builder
    {
        return $builder->addSelect('u.name AS creator');
    }

    /**
     * Get the user who approved the order. This select statement assumes that the main
     * query already has a join to the users table using the approved_by column
     *
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeApprover(Builder $builder): Builder
    {
        return $builder->addSelect('app.name AS approver');
    }

    /***************************
     * E N D  O F  S C O P E S *
     ***************************/

    /**
     * Approve order
     *
     * @param User $user
     * @param bool $sync
     *
     * @return bool
     */
    public function approve(User $user, $sync = true)
    {
        $this->status = self::STATUS_APPROVED;
        $this->approved_by = $user->id;
        $this->approved_at = now()->toDateTimeString();
        $this->save();

        // sync order items to parent status
        if ($sync) {
            $this->syncOrderItemStatus();
        }

        return true;
    }

    /**
     * Cancel and order
     *
     * @param bool $sync
     *
     * @return bool
     */
    public function cancel($sync = true)
    {
        $this->status = self::STATUS_CANCELLED;
        $this->save();

        // sync order items to parent status
        if ($sync) {
            $this->syncOrderItemStatus();
        }

        return true;
    }

    /**
     * Sync order's order items' status to the parent status
     *
     * @return bool
     */
    public function syncOrderItemStatus(): bool
    {
        /**
         * Let's make sure that the parent status is present in the supported statuses
         * of the stock_order_items model
         */
        if (!in_array($this->status, StockOrderItem::ORDER_STATUSES)) {
            return false;
        }

        return StockOrderItem::where('stock_order_items.stock_order_id', $this->id)
            ->update([
                'status' => $this->status
            ]);
    }

    /**
     * Clone an order's order items to a new order
     *
     * @param StockOrder $newOrder
     * @param User $user
     *
     * @return array
     */
    public function cloneItems(self $newOrder, User $user): array
    {
        $newItems = [];

        // get the order's order items
        $items = $this->orderItems()->select([
            'stock_level_id',
            'order_qty'
        ])->get();

        // loop through all the order items
        foreach ($items as $item) {
            // replicate
            $newItem = $item->replicate();

            // reset the necessary fields
            $newItem->stock_order_id = $newOrder->id;
            $newItem->status = StockOrderItem::STATUS_PENDING;
            $newItem->created_by = $user->id;
            $newItem->save();

            $newItems[] = $newItem;
        }

        return $newItems;
    }
}
