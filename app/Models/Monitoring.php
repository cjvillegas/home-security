<?php

namespace App\Models;

use App\Events\UpdateBlockStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Monitoring extends Model
{
    use HasFactory, SoftDeletes;

    const STATUS_NORMAL = 1;
    const STATUS_BURNING = 2;
    const STATUS_BURGLAR = 3;

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'ip_address',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::updated(function (self $monitoring) {
            event(new UpdateBlockStatus($monitoring, 'status-change'));
        });

        static::deleted(function (self $monitoring) {
            event(new UpdateBlockStatus($monitoring, 'deleted'));
        });
    }
}
