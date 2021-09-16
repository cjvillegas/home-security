<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftAssignment extends Model
{
    use HasFactory;

    /**
     * Mass assignable fields
     *
     * @var array
     */
    protected $fillable = [
        'serial_id',
        'folder_name',
        'scheduled_date',
        'work_date',
        'created_at',
        'updated_at',
    ];

    /**************
    * S C O P E S *
    **************/
    /**
     * Add a condition to filter only data that is equal to the given date
     *
     * @param Builder $query
     * @param string $date
     *
     * @return Builder
     */
    public function scopeFilterInDate(Builder $query, string $date): Builder
    {
        return $query->whereRaw("DATE_FORMAT(shift_assignments.scheduled_date, '%Y-%m-%d') = '{$date}'");
    }

    /**
     * Filter shift assignments based on the folder names
     *
     * @param Builder $query
     * @param array $folders
     *
     * @return Builder
     */
    public function scopeFilterByFolderName(Builder $query , array $folders): Builder
    {
        return $query->where(function ($query) use ($folders) {
            foreach ($folders as $key => $folder) {
                if ($key === 0) {
                    $query->where('shift_assignments.folder_name', 'like', "%{$folder}%");
                } else {
                    $query->orWhere('shift_assignments.folder_name', 'like', "%{$folder}%");
                }
            }
        });
    }

    /**************************
    * E N D  O F  S C O P E S *
    **************************/
}
