<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimeClock extends Model
{
    use SoftDeletes;

    /**
     * Mass assignable fields
     *
     * @var array
     */
    protected $fillable = [
        'employee_id',
        'clock_num',
        'swiped_at',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /********************
    * R E L A T I O N S *
    ********************/

    /**
     * Get the user who created this sequence
     *
     * @return BelongsTo
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /********************************
    * E N D  O F  R E L A T I O N S *
    ********************************/

    /**
     * Get the most recent punch in
     *
     * @return mixed
     */
    public function getLatestData(): ?string
    {
        $timeClock = self::orderBy('trans_id', 'desc')
            ->first();

        return optional($timeClock)->trans_id;
    }
}
