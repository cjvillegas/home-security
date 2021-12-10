<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OvertimeBooking extends Model
{
    use SoftDeletes;
    use HasFactory;

    /**
     * Toggle Lock/Unlock on Slot
     *
     * @return void
     */
    public function toggleLock()
    {
        if ($this->is_locked) {
            $this->update(['is_locked', false]);
        } else {
            $this->update(['is_locked', true]);
        }
    }
}
