<?php

namespace App\Models;

use App\Traits\ColorAttributeTrait;
use \DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use SoftDeletes;
    use HasFactory;
    use ColorAttributeTrait;

    public $table = 'teams';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'target',
        'folder_names',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Appends custom attribute every model instance
     *
     * @var array
     */
    protected $appends = [
        'color',
    ];

    /**
     * Cast variables to specified data types
     *
     * @var array
     */
    protected $casts = [
        'folder_names' => 'array',
    ];

    /**
     * Relation to Machine Counter's Model
     *
     * @return HasMany
     */
    public function machineCounters(): HasMany
    {
        return $this->hasMany(MachineCounter::class);
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
