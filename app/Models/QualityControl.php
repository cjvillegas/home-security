<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QualityControl extends Model
{
    use SoftDeletes, HasFactory;

    /**
     * The mass assignable attributes of this model.
     * If you want to make a field mass assignable during
     * create or update, declare it here.
     *
     * @var array
     */
    protected $fillable = [
        'qc_code',
        'description',
        'is_active'
    ];

    /**
     * Cast variables to specified data types
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];
}
