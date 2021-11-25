<?php

namespace App\Models;

use App\Models\Order\OrderInvoice;
use App\Models\ProcessSequence\ProcessSequence;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;
    use HasFactory;

    /**
     * Mass assignable fields
     *
     * @var string[]
     */
    protected $fillable = [
        'blind_id',
        'order_no',
        'customer',
        'customer_order_no',
        'quantity',
        'blind_type',
        'blind_status',
        'order_entered_by',
        'serial_id',
        'despatched_at',
        'ordered_at',
        'required_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /*******************
    * F U N C T I O N  *
    *******************/
    /**
     * Get the most latest blind_id entry in the table
     *
     * @return int|null
     */
    public static function getLatestBlindId()
    {
        return self::orderBy('blind_id', 'desc')->first()->blind_id ?? null;
    }

    /**
     * Get the most latest blind_id entry in the table
     *
     * @return int|null
     */
    public static function getLatestSerialId()
    {
        return self::orderBy('serial_id', 'desc')->first()->serial_id ?? null;
    }

    /**************************
    * E N D  F U N C T I O N  *
    **************************/

    /********************
    * R E L A T I O N S *
    ********************/
    public function scanners()
    {
        return $this->hasMany(Scanner::class, 'blindid', 'serial_id');
    }

    /**
     * This will return list of Order Trackings
     *
     * @return HasOne
     */
    public function orderTrackings(): HasOne
    {
        return $this->hasOne(OrderTracking::class, 'order_no', 'order_no');
    }

    /**
     * @return HasOne
     */
    public function processSequence(): HasOne
    {
        return $this->hasOne(ProcessSequence::class, 'name', 'product_type');
    }

    /**
     * Get the most recent scanner data of an employee
     *
     * @return HasOne
     */
    public function latestScanner(): HasOne
    {
        return $this->hasOne(Scanner::class, 'blindid', 'serial_id')->orderBy('scannedtime', 'desc');
    }

    /**
     * Get the oldest scanner
     *
     * @return HasOne
     */
    public function oldestScanner(): HasOne
    {
        return $this->hasOne(Scanner::class, 'blindid', 'serial_id')->orderBy('scannedtime', 'asc');
    }

    /**
     * Get Order Invoice info per Blind
     *
     * @return HasOne
     */
    public function orderInvoice(): HasOne
    {
        return $this->hasOne(OrderInvoice::class, 'order_no', 'order_no');
    }


    /**
     * Get Order's customer info
     *
     * @return BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'account_code', 'code');
    }
    /********************************
    * E N D  O F  R E L A T I O N S *
    ********************************/
}
