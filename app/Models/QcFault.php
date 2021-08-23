<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}
