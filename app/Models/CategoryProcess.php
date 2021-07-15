<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryProcess extends Model
{
    use SoftDeletes;

    /**
     * Declare the model variables that are to be mass assignable
     *
     * @var array
     */
    protected $fillable = [
        'process_id',
        'process_category_id',
    ];

}
