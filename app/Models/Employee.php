<?php

namespace App\Models;

use \DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;
    use HasFactory;

    public $table = 'employees';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'fullname',
        'barcode',
        'pin_code',
        'target',
        'shift_id',
        'team_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    /*********************
    * F U N C T I O N S  *
    *********************/

    /**
     * Generates a unique barcode for the employee. This code will first check the last barcode entry
     * then it will recursively call the method until it generates a new barcode that doesn't match anything from the DB
     *
     * @param int $code
     *
     * @return string
     */
    public function generateBarcode(int $code = null): string
    {
        if (!$code) {
            $lastEmployee = self::orderBy('id', 'DESC')->first();
            $code = (int) substr($lastEmployee->barcode, 1);
        }

        $code++;
        $barcode = "E{$code}";

        if (self::where('barcode', $barcode)->exists()) {
            $this->generateBarcode($code);
        }

        return "E{$code}";
    }

    /****************************
    * F U N C T I O N S  E N D *
    ****************************/
}
