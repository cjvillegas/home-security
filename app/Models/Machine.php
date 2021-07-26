<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
     * Set Machine's Status
     *
     * @param integer $value
     *
     * @return string
     */
    public function getStatusAttribute( $value): string
    {
        return $value === 1 ? 'Active' : 'Inactive';
    }

    /**
     * Relation to Machine Counter's model
     *
     * @return HasMany
     */
    public function machineCounters()
    {
        return $this->hasMany(MachineCounter::class);
    }
}
