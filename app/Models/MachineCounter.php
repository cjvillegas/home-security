<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineCounter extends Model
{
    use HasFactory;
    protected $guarded = [];

    /********************
    * R E L A T I O N S *
    ********************/

    /**
     * Relation to Machine's Model
     *
     * @return belongsTo
     */
    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    /**
     * Relation to Employee's Model
     *
     * @return belongsTo
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Relation to Team's model
     *
     * @return belongsTo
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Relation to Shift's model
     *
     * @return belongsTo
     */
    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    /********************************
    * E N D  O F  R E L A T I O N S *
    ********************************/

}
