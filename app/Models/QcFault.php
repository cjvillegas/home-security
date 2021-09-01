<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class QcFault extends Model
{
    use SoftDeletes;

    /**
     * Mass assignable fields
     *
     * @var array
     */
    protected $fillable = [
        'quality_control_id',
        'employee_id',
        'user_id',
        'process_id',
        'scanner_id',
        'description',
        'operation_date',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /********************
    * R E L A T I O N S *
    ********************/

    /**
     * Get the QC Code of this Qc tag
     *
     * @return BelongsTo
     */
    public function qualityControl(): BelongsTo
    {
        return $this->belongsTo(QualityControl::class);
    }

    /**
     * Get the employee of this Qc tag
     *
     * @return BelongsTo
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the user who created the qc tag
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the process related to this qc fault
     *
     * @return BelongsTo
     */
    public function process(): BelongsTo
    {
        return $this->belongsTo(Process::class);
    }

    /**
     * Get the scanner data related to this qc tag
     *
     * @return BelongsTo
     */
    public function scanner(): BelongsTo
    {
        return $this->belongsTo(Scanner::class);
    }

    /********************************
    * E N D  O F  R E L A T I O N S *
    ********************************/

    /**************
    * S C O P E S *
    **************/
    /**
     * Add where clause query to filter data based on employee
     *
     * @param Builder $query
     * @param mixed $employee
     *
     * @return Builder
     */
    public function scopeFilterByEmployee(Builder $query, $employee): Builder
    {
        // checks if the passed data for employee is array of employee id
        if (is_array($employee)) {
            $query->whereIn('qc_faults.employee_id', $employee);
        } else {
            $query->where('qc_faults.employee_id', $employee);
        }

        return $query;
    }

    /**
     * Add where clause query to filter data based on users
     *
     * @param Builder $query
     * @param mixed $users
     *
     * @return Builder
     */
    public function scopeFilterByUser(Builder $query, $users): Builder
    {
        // checks if the passed data for employee is array of employee id
        if (is_array($users)) {
            $query->whereIn('qc_faults.user_id', $users);
        } else {
            $query->where('qc_faults.user_id', $users);
        }

        return $query;
    }

    /**
     * Add where clause query to filter data based on processes
     *
     * @param Builder $query
     * @param mixed $processes
     *
     * @return Builder
     */
    public function scopeFilterByProcess(Builder $query, $processes): Builder
    {
        // checks if the passed data for employee is array of employee id
        if (is_array($processes)) {
            $query->whereIn('qc_faults.process_id', $processes);
        } else {
            $query->where('qc_faults.process_id', $processes);
        }

        return $query;
    }

    /**
     * Add where clause query to filter data based on processes
     *
     * @param Builder $query
     *
     * @param mixed $qualityControls
     *
     * @return Builder
     */
    public function scopeFilterByQualityControl(Builder $query, $qualityControls): Builder
    {
        // checks if the passed data for employee is array of employee id
        if (is_array($qualityControls)) {
            $query->whereIn('qc_faults.quality_control_id', $qualityControls);
        } else {
            $query->where('qc_faults.quality_control_id', $qualityControls);
        }

        return $query;
    }

    /**
     * Filter qc faults using a search string and based on the passed columns to
     * do the search
     *
     * @param Builder $query
     * @param array $columns
     * @param string $searchString
     *
     * @return Builder
     */
    public function scopeFilterBySearch(Builder $query, array $columns, string $searchString): Builder
    {
        if (empty($columns) || empty($searchString)) {
            return $query;
        }

        return $query->where(function ($innerQuery) use ($columns, $searchString) {
            // loop through all the provided columns and add the where clause
            foreach ($columns as $index => $col) {
                if ($index === 0) {
                    $innerQuery->where($col, 'like', "%$searchString%");
                } else {
                    $innerQuery->orWhere($col, 'like', "%$searchString%");
                }
            }
        });
    }

    /**
     * Add a scope query to retrieve qc fault data based on the given date range
     *
     * @param Builder $query
     * @param array $dates
     *
     * @return Builder
     */
    public function scopeFilterInRange(Builder $query, array $dates)
    {
        if (empty($dates)) {
            return $query;
        }

        return $query->whereBetween('qc_faults.created_at', $dates);
    }

    /**************************
    * E N D  O F  S C O P E S *
    **************************/
}
