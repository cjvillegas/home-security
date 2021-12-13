<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class OvertimeBooking extends Model
{
    use SoftDeletes;
    use HasFactory;

    /**
     * Mass assignable fields
     *
     * @var string[]
     */
    protected $fillable = [
        'available_date',
        'working_hours',
        'is_locked'
    ];
}
