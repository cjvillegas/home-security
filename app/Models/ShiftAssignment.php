<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftAssignment extends Model
{
    use HasFactory;

    /**
     * Mass assignable fields
     *
     * @var array
     */
    protected $fillable = [
        'serial_id',
        'folder_name',
        'schedule_date',
        'work_date',
        'created_at',
        'updated_at',
    ];
}
