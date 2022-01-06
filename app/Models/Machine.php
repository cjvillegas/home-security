<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Machine extends Model
{
    use SoftDeletes, HasFactory;

    /**
     * Mass assignable fields
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'serial_no',
        'location',
        'supplier',
        'model',
        'machine_target',
        'parameter_1',
        'parameter_2',
        'parameter_3',
        'parameter_4',
        'parameter_5',
        'parameter_6',
        'parameter_7',
        'parameter_8',
        'parameter_9',
        'parameter_10',
        'status',
        'process_barcode',
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
        'status' => 'boolean',
    ];

    /**
     * Relation to Machine Counter's model
     *
     * @return HasMany
     */
    public function machineCounters(): HasMany
    {
        return $this->hasMany(MachineCounter::class);
    }

    /**
     * Relation to Process' model
     *
     * @return BelongsTo
     */
    public function process(): BelongsTo
    {
        return $this->belongsTo(Process::class, 'barcode', 'process_barcode');
    }
}
