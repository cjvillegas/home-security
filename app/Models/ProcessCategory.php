<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProcessCategory extends Model
{
    use SoftDeletes;

    /**
     * Declare the model variables that are to be mass assignable
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'name',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /********************
    * R E L A T I O N S *
    ********************/
    /**
     * Retrieve list of this process category's processes.
     * This method queries from a junction table named category_processes
     * since the relationship is defined by many-to-many.
     *
     * @return BelongsToMany
     */
    public function processes()
    {
        return $this->belongsToMany(Process::class,'category_processes');
    }
    /********************************
    * E N D  O F  R E L A T I O N S *
    ********************************/
}
