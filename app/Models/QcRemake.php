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

    protected $casts = [
        'question_key' => 'array',
    ];

    const VERIFICATION_CHECKLISTS = [
        [
            'key' => 1,
            'value' => 'I can confirm that the Blind Width is: '
        ],
        [
            'key' => 2,
            'value' => 'I can confirm that the Blind Drop is: '
        ],
        [
            'key' => 3,
            'value' => 'I can confirm that the Blind Fabric is: '
        ],
        [
            'key' => 4,
            'value' => 'I can confirm that the Blind has been fully tested: '
        ],
        [
            'key' => 5,
            'value' => 'I can confirm that all the required Parts are present: '
        ],
        [
            'key' => 6,
            'value' => 'I can confirm that the corrent Brackets are included with the Blind: '
        ],

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
