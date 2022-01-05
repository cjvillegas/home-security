<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Export extends Model
{
    use HasFactory;

    /**
     * Export Types
     */
    // qc report type
    const QC_FAULT_EXPORT_REPORT = 'quality_control_export';

    // team status export report
    const TEAM_STATUS_EXPORT_REPORT = 'team_status_report';

    // scanners raw data
    const SCANNERS_RAW_DATA = 'scanners_raw_data';

    // the time and attendance report
    const TIME_AND_ATTENDANCE_REPORT = 'time_and_attendance';

    // manufactured blinds report
    const MANUFACTURED_BLIND_REPORT = 'manufactured_blind_export';

    // Fire register report
    const FIRE_REGISTER_REPORT = 'fire_register_export';

    // target performance report
    const TARGET_PERFORMANCE_REPORT = 'target_performance_export';

    // shift performance report
    const SHIFT_PERFORMANCE_REPORT = 'shift_performance_report';

    // orders export report type
    const ORDERS_EXPORT_REPORT = 'orders_export';

    // order picking PDF export
    const ORDER_PICKING_PDF_EXPORT = 'order_picking';

    /**
     * Export Status
     */
    const EXPORT_STATUS_IN_PROGRESS = 1; // exporting is in progress
    const EXPORT_STATUS_COMPLETED = 2; // exporting is completed
    const EXPORT_STATUS_FAILED = 3; // exporting has failed
    const EXPORT_STATUS_KILLED = 4; // exporting has been killed

    /**
     * Mass assignable fields
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'uid',
        'url',
        'status',
        'type',
        'filters',
        'is_killed',
        'done_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Cast variables to specified data types
     *
     * @var array
     */
    protected $casts = [
        'is_killed' => 'boolean',
        'filters' => 'json',
    ];

    /********************
    * R E L A T I O N S *
    ********************/

    /**
     * Get the user who created the qc tag
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /********************************
    * E N D  O F  R E L A T I O N S *
    ********************************/
}
