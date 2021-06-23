<?php

namespace App\Models;

use \DateTimeInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;
    use HasFactory;

    public $table = 'orders';

    protected $dates = [
        'despatch_date',
        'ordered',
        'required',
        'scheduled_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'blindid',
        'order_no',
        'customer',
        'cust_ord_ref',
        'cust_ord_no',
        'quantity',
        'blind_type',
        'range',
        'colour',
        'stock_code',
        'man_width',
        'man_drop',
        'blind_status',
        'despatch_date',
        'ordered',
        'required',
        'scheduled_date',
        'roller_table',
        'remake',
        'same_day_despatch',
        'over_size',
        'man_location',
        'order_entered_by',
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

}
