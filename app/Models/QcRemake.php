<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class QcRemake extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $table = 'qc_remakes';

    /**
     * Mass assignable fields
     *
     * @var array
     */
    protected $fillable = [
        'report_no',
        'user_id',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Qc Remake Validated Blinds
     *
     * @return HasMany
     */
    public function validatedBlinds(): HasMany
    {
        return $this->hasMany(QcRemakeValidated::class, 'qc_remake_id');
    }

    /**
     * Qc remake's user
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Qc remake's Order info
     *
     * @return BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_no', 'order_no');
    }

    /*********************
    * F U N C T I O N S  *
    *********************/
    /**
     * Generates a unique Report Number for the employee. This code will first check the last report_no entry
     * then it will recursively call the method until it generates a new report_no that doesn't match anything from the DB
     *
     * @param int $code
     *
     * @return string
     */
    public function generateReportNumber(int $code = null): string
    {
        if (!$code) {
            $lastQcRemake = self::orderBy('id', 'DESC')->first();

            $code = is_null($lastQcRemake) ? 1 : substr($lastQcRemake->report_no, 4);
        }

        $code++;
        $reportNumber = "QCV-{$code}";

        if (self::where('report_no', $reportNumber)->exists()) {
            $this->generateReportNumber($code);
        }

        return "QCV-{$code}";
    }
    /****************************
    * F U N C T I O N S  E N D *
    ****************************/
}
