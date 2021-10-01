<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use function Symfony\Component\String\b;

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
     * @param string $dates
     *
     * @return Builder
     */
    public function scopeFilterInBetweenDates(Builder $query, array $dates): Builder
    {
        return $query->whereBetween('shift_assignments.scheduled_date', $dates);
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

    /**
     * Filters shift assignments based on the folder names
     *
     * @param Builder $builder
     * @param array $folders
     *
     * @return Builder
     */
    public function scopeFilterInFolderName(Builder $builder, array $folders): Builder
    {
        // sanity check: has folders
        if (!empty($folders)) {
            $builder->whereIn('shift_assignments.folder_name', $folders);
        }

        return $builder;
    }

    /**************************
    * E N D  O F  S C O P E S *
    **************************/
}
