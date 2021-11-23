<?php

namespace App\Models;

use Carbon\Carbon;
use \DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class Shift extends Model
{
    use SoftDeletes;
    use HasFactory;

    // define the start and of time of each shift
    const SHIFT_ONE_TIME = ['06:00:00', '13:59:59'];
    const SHIFT_TWO_TIME = ['14:00:00', '21:59:59'];
    const SHIFT_THREE_TIME = ['22:00:00', '05:59:59'];

    // list for convenience
    const SHIFT_TIME_LIST = [
        'shift_one' => self::SHIFT_ONE_TIME,
        'shift_two' => self::SHIFT_TWO_TIME,
        'shift_three' => self::SHIFT_THREE_TIME,
    ];

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

    /**
     * We often have dates filter in our application and most of these filters
     * will use the shifts time. This method will return a date range that will
     * respect all the shifts start and end time.
     *
     * i.e.
     * start => 2021-10-01 06:00:00
     * end => 2021-10-10 05:59:59
     *
     * @param mixed $start
     * @param mixed $end
     *
     * @return array|bool
     */
    public static function getShiftsStartAndEndBased($start, $end): ?array
    {
        // carbonized the dates
        $start = $start instanceof Carbon ? $start : Carbon::parse($start);
        $end = $end instanceof Carbon ? $end : Carbon::parse($end);

        // sanity check: start and end should be valid dates
        if (!$start->isValid() || !$end->isValid()) {
            Log::warning("Shift: No valid dates found in getShiftsStartAndEndBased", [
                'start' => $start,
                'end' => $end
            ]);

            return false;
        }

        // get the actual dates
        $shiftStart = "{$start->format('Y-m-d')} 06:00:00";
        $shiftEnd = Carbon::parse("{$end->format('Y-m-d')} 05:59:59")->addDay()->format('Y-m-d H:i:s');

        return [
            $shiftStart,
            $shiftEnd
        ];
    }
    /******************************************
    * E N D  C U S T O M  P R O P E R T I E S *
    ******************************************/

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
