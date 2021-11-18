<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QcRemakeValidated extends Model
{
    use HasFactory;

    protected $fillable = [
        'blind_id',
        'barcode',
        'question_key',
        'reason',
        'is_fully_verified'
    ];

    protected $casts = [
        'question_key' => 'array'
    ];

    /**
     * Qc Remake
     *
     * @return BelongsTo
     */
    public function qcRemake(): BelongsTo
    {
        return $this->belongsTo(QcRemake::class);
    }

    /**
     * Blinds
     *
     * @return BelongsTo
     */
    public function blind(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'blind_id', 'serial_id');
    }
}
