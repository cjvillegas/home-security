<?php

namespace App\Models\InHouse;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockItems extends Model
{
    use HasFactory;

    protected $guarded = [];


    /**
     * Set Stock Item's Status
     *
     * @param  mixed $value
     * @return string $value
     */
    public function getStatusAttribute($value): string
    {
        return $value === 1 ? 'Active' : 'Inactive';
    }
}
