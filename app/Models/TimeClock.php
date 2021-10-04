<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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

    /**************
    * S C O P E S *
    **************/

    /**
     * Add a condition to filter only data where the specified date column is
     * in between the passed dates.
     *
     * @param Builder $query
     * @param array $dates
     *
     * @return Builder
     */
    public function scopeFilterInBetweenDates(Builder $query, array $dates): Builder
    {
        return $query->whereBetween('time_clocks.swiped_at', $dates);
    }

    /**
     * Add condition to retrieve only data where employee_id is in the collection
     * of employee ids parameter
     *
     * @param Builder $builder
     * @param mixed $employee
     *
     * @return Builder
     */
    public function scopePerEmployee(Builder $builder, $employee): Builder
    {
        $ids = is_array($employee) ? $employee : [$employee];

        return $builder->whereIn('employee_id', $ids);

    }

    /**************************
    * E N D  O F  S C O P E S *
    **************************/
}
