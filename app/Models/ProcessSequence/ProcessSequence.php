<?php
namespace App\Models\ProcessSequence;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProcessSequence extends Model
{
    use SoftDeletes;

    /**
     * Mass assignable fields
     *
     * @var array
     */
    protected $fillable = [
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
     * Get the user who created this sequence
     *
     * @return BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who updated this sequence
     *
     * @return BelongsTo
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Process Sequence Steps
     *
     * @return HasMany
     */
    public function steps(): HasMany
    {
        return $this->hasMany(ProcessSequenceLink::class);
    }


    /********************************
    * E N D  O F  R E L A T I O N S *
    ********************************/
}
