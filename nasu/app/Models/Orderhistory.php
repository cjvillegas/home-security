<?php

namespace App\Models;

use \DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Orderhistory extends Model
{
    use SoftDeletes;
    use HasFactory;

    public $table = 'orderhistories';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'order_number_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function order_number()
    {
        return $this->belongsTo(Order::class, 'order_number_id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
