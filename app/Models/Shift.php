<?php

namespace App\Models;

use \DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shift extends Model
{
    use SoftDeletes;
    use HasFactory;

    const SHIFT_ONE_TIME = ['06:00', '14:00'];
    const SHIFT_TWO_TIME = ['14:00', '22:00'];
    const SHIFT_THREE_TIME = ['22:00', '06:00'];

    public $table = 'shifts';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
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
        'shift_start',
        'shift_end',
    ];

    /***********************************
    * C U S T O M  P R O P E R T I E S *
    ************************************/
    /**
     * Retrieve the start of shift for each shifts.
     * These data is statically defined.
     *
     * @return string
     */
    public function getShiftStartAttribute(): string
    {
        $start = '';

        if ($this->id === 1) {
            $start = '06:00';
        }

        if ($this->id === 2) {
            $start = '14:00';
        }

        if ($this->id === 3) {
            $start = '22:00';
        }
        return $start;
    }

    /**
     * Retrieve the end of shift for each shifts.
     * These data is statically defined.
     *
     * @return string
     */
    public function getShiftEndAttribute(): string
    {
        $start = '';

        if ($this->id === 1) {
            $start = '14:00';
        }

        if ($this->id === 2) {
            $start = '22:00';
        }

        if ($this->id === 3) {
            $start = '06:00';
        }

        return $start;
    }
    /******************************************
    * E N D  C U S T O M  P R O P E R T I E S *
    ******************************************/


    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
