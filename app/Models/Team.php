<?php

namespace App\Models;

use App\Helpers\StringGenericHelper;
use \DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use SoftDeletes;
    use HasFactory;

    public $table = 'teams';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'target',
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

    /***********************************
    * C U S T O M  P R O P E R T I E S *
    ************************************/
    /**
     * Sets a custom RGBA color for a team
     * This is mainly used for visuals
     *
     * @return string
     */
    public function getColorAttribute()
    {
        return StringGenericHelper::generateRgbaString(1);
    }
    /******************************************
    * E N D  C U S T O M  P R O P E R T I E S *
    ******************************************/


    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
