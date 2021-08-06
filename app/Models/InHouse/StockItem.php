<?php

namespace App\Models\InHouse;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    /**
     * Set Stock Item's Status
     *
     * @param  mixed $value
     *
     * @return string $value
     */
    public function getStatusAttribute($value): string
    {
        return $value === 1 ? 'Active' : 'Inactive';
    }
}
