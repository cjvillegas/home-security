<?php

namespace App\Models;

use App\Traits\ColorAttributeTrait;
use \DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;
    use HasFactory;
    use ColorAttributeTrait;

    public $table = 'employees';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * The mass assignable attributes of this model.
     * If you want to make a field mass assignable during
     * create or update, declare it here.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'fullname',
        'barcode',
        'pin_code',
        'target',
        'standard_working_hours',
        'clock_num',
        'shift_id',
        'team_id',
        'is_active',
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
        'is_active' => 'boolean',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::created(function (self $employee) {
            // when a new employee is created, generate a new user
            $employee->generateUser();
        });
    }

    /********************
     * R E L A T I O N S *
     ********************/
    /**
     * Retrieve employee's shift
     *
     * @return BelongsTo
     */
    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }

    /**
     * Relation to Machine Counters
     *
     * @return HasMany
     */
    public function machineCounters()
    {
        return $this->hasOne(MachineCounter::class);
    }

    /**
     * Retrieves employee's team
     *
     * @return BelongsTo
     */
    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    /**
     * Retrieves employee's user
     *
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the qc faults of this employee
     *
     * @return HasMany
     */
    public function qcFaults(): HasMany
    {
        return $this->hasMany(QcFault::class);
    }

    /********************************
    * E N D  O F  R E L A T I O N S *
    ********************************/

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

    /**
     * This method will create a new user when a new employee is created.
     *
     * @return mixed
     */
    public function generateUser()
    {
        // sanity check if employee already has user, cancel execution
        if ($this->user_id && $this->user) {
            return false;
        }

        $user = new User;
        $user->name = $this->fullname ?: $this->barcode;
        $user->email = "{$this->barcode}@stylebyglobal.com";
        $user->barcode = $this->barcode;
        $user->password = bcrypt($this->pin_code);
        $user->created_at = now();
        $saved = $user->save();

        // when a user is successfully created, link it to the employee
        if ($saved) {
            $this->user_id = $user->id;
            $this->save();

            $user->assignToRoleByTitle('Employee');
        }

        return $this;
    }

    public function updateUser()
    {
        $this->user->name = $this->fullname;
        $this->user->name = $this->fullname;
    }

    /****************************
    * F U N C T I O N S  E N D *
    ****************************/
}
