<?php

namespace App\Models;

use App\Models\Order\OrderInvoice;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Scanner extends Model
{
    use SoftDeletes;
    use HasFactory;

    public $table = 'scanners';

    protected $dates = [
        'scannedtime',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'scannedtime',
        'employeeid',
        'processid',
        'blindid',
        'created_at',
        'updated_at',
        'deleted_at',
        'flag',
    ];

    /**
     * Casts a DB column data type to a specified datatype in PHP
     *
     * @var array
     */
    protected $casts = [
        'blindid' => 'integer'
    ];

    /********************
    * R E L A T I O N S *
    ********************/

    /**
     * Retrieve order of this scanner
     *
     * @return BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'blindid', 'serial_id');
    }

    /**
     * Retrieve employee
     *
     * @return BelongsTo
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employeeid', 'barcode');
    }

    /**
     * Retrieve process
     *
     * @return BelongsTo
     */
    public function process()
    {
        return $this->belongsTo(Process::class, 'processid', 'barcode');
    }

    /**
     * Get the qc faults related to this scanner
     *
     * @return HasOne
     */
    public function qcFault(): HasOne
    {
        return $this->hasOne(QcFault::class, 'scanner_id', 'id');
    }

    /**
     * Get the order invoiced related to this Scanner
     *
     * @return HasOne
     */
    public function orderInvoice(): HasOne
    {
        return $this->hasOne(OrderInvoice::class, 'order_no', 'blindid');
    }

    /********************************
    * E N D  O F  R E L A T I O N S *
    ********************************/

    /**************
    * S C O P E S *
    **************/

    /**
     * Filter by employees
     *
     * @param Builder $builder
     * @param array $employees
     *
     * @return Builder
     */
    public function scopeByEmployees(Builder $builder, array $employees)
    {
        return $builder->whereIn('employeeid', $employees);
    }

    /**
     * Filter by process
     *
     * @param Builder $builder
     * @param array $processes
     *
     * @return Builder
     */
    public function scopeByProcesses(Builder $builder, array $processes)
    {
        return $builder->whereIn('processid', $processes);
    }

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
        return $query->whereBetween('scanners.scannedtime', $dates);
    }

    /**************************
    * E N D  O F  S C O P E S *
    **************************/
}
