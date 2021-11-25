<?php

namespace App\Models;

use App\Traits\ColorAttributeTrait;
use \DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Process extends Model
{
    use SoftDeletes;
    use HasFactory;
    use ColorAttributeTrait;

    public $table = 'processes';

    /**
     * @var string[]
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Mass assignable fields
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'barcode',
        'process_target',
        'new_joiner_target',
        'process_manufacturing_time',
        'stop_start_button_required',
        'team_trade_target',
        'team_internet_target',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Cast variables to specified data types
     *
     * @var array
     */
    protected $casts = [
        'stop_start_button_required' => 'boolean',
    ];

    /**
     * Appends custom attribute every model instance
     *
     * @var array
     */
    protected $appends = [
        'color',
    ];

    /********************
    * R E L A T I O N S *
    ********************/

    /**
     * Retrieve list of this process' process categories.
     * This method queries from a junction table named category_processes
     * since the relationship is defined by many-to-many
     *
     * @return BelongsToMany
     */
    public function processCategories()
    {
        return $this->belongsToMany(ProcessCategory::class,'category_processes');
    }

    /**
     * Get the qc faults related to this process
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
}
