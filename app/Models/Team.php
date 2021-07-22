<?php

namespace App\Models;

use App\Traits\ColorAttributeTrait;
use \DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Relation to Machine Counter's Model
     *
     * @return void
     */
    public function machineCounters()
    {
        return $this->hasMany(MachineCounter::class);
    }

    /**
     * Appends custom attribute every model instance
     *
     * @var array
     */
    protected $appends = [
        'color',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
