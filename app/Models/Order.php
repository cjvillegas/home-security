<?php

namespace App\Models;

use \DateTimeInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;
    use HasFactory;

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

    public function getDespatchDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setDespatchDateAttribute($value)
    {
        $this->attributes['despatch_date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function getOrderedAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setOrderedAttribute($value)
    {
        $this->attributes['ordered'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function getRequiredAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setRequiredAttribute($value)
    {
        $this->attributes['required'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function getScheduledDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setScheduledDateAttribute($value)
    {
        $this->attributes['scheduled_date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

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
    /********************************
    * E N D  O F  R E L A T I O N S *
    ********************************/
}
