<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MachineCounter extends Model
{
    use HasFactory;
    protected $guarded = [];

    /********************
    * R E L A T I O N S *
    ********************/

    /**
     * Relation to Machine's Model
     *
     * @return BelongsTo
     */
    public function machine(): BelongsTo
    {
        return $this->belongsTo(Machine::class);
    }

    /**
     * Relation to Employee's Model
     *
     * @return BelongsTo
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Relation to Team's model
     *
     * @return BelongsTo
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Relation to Shift's model
     *
     * @return BelongsTo
     */
    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    /********************************
    * E N D  O F  R E L A T I O N S *
    ********************************/

    /**
     * Get today's Machine Counters
     * @return query
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', now());
    }

    /**
     * Get yesterday's Machine Counters
     * @return query
     */
    public function scopeYesterday($query)
    {
        return $query->whereDate('created_at', date('Y-m-d', strtotime("-1 days")));
    }

}
