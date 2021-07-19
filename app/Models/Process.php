<?php

namespace App\Models;

use App\Traits\ColorAttributeTrait;
use \DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Process extends Model
{
    use SoftDeletes;
    use HasFactory;
    use ColorAttributeTrait;

    public $table = 'processes';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'barcode',
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
    /********************************
    * E N D  O F  R E L A T I O N S *
    ********************************/

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
