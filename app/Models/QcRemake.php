<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class QcRemake extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $table = 'qc_remakes';

    protected $fillable = [
        'report_no',
        'barcode',
        'user_id',
        'qc_remake_validated_id',
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
        return $this->hasMany(QcRemakeValidated::class, 'qc_remake_validated_id');
    }
}
