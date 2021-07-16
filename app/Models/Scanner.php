<?php

namespace App\Models;

use \DateTimeInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
    ];

    /**
     * Custom properties that will appended to all instances
     * and data collection of this scanners
     *
     * @var array
     */
    protected $appends = [
        'is_checked'
    ];

    public function getIsCheckedAttribute()
    {
        return '';
    }

    public function getScannedtimeAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('panel.date_format') . ' ' . config('panel.time_format')) : null;
    }

    public function setScannedtimeAttribute($value)
    {
        $this->attributes['scannedtime'] = $value ? Carbon::createFromFormat(config('panel.date_format') . ' ' . config('panel.time_format'), $value)->format('Y-m-d H:i:s') : null;
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    /********************
    * R E L A T I O N S *
    ********************/
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
    /********************************
    * E N D  O F  R E L A T I O N S *
    ********************************/

}
