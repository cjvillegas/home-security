<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    public function getMostRecentUpdateDate()
    {
        $latest = self::latest('date_time_updated')->first();

        return optional($latest)->date_time_updated;
    }
}
