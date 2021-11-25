<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;
    use HasFactory;

    /**
     * Mass assignable attributes of this model.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'name',
        'zoho_crm_id'
    ];
}
