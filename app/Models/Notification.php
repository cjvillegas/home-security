<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    /**
     * Cast variables to specified data types
     *
     * @var array
     */
    protected $casts = [
        'data' => 'array',
        'id' => 'string'
    ];
}
