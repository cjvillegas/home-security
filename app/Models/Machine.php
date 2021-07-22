<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    use HasFactory;


    /**
     * Alternative for fillable
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Relation to Machine Counter's model
     *
     * @return void
     */
    public function machineCounters()
    {
        return $this->hasMany(MachineCounter::class);
    }
}
