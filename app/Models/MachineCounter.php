<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineCounter extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

}
