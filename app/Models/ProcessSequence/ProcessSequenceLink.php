<?php
namespace App\Models\ProcessSequence;

use App\Models\Process;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcessSequenceLink extends Model
{
    const UPDATED_AT = null;
    const DELETED_AT = null;
    /**
     * Mass assignable fields
     *
     * @var array
     */
    protected $fillable = [
        'process_sequence_id',
        'process_id',
        'previous_step_id',
        'next_step_id',
        'process_id',
        'order',
        'created_at',
    ];

    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['processSequence'];

    /**
     * Adjusts sequence order. This will increment all `order` starting from the `order` number conflicting the
     * current model's `order` number. This is typically used when adding new sequence in between of two sequences.
     *
     * @return void
     */
    public function incrementOtherSequencesOrder()
    {
        self::where('order', '>=', $this->order)
            ->where('process_sequence_id', $this->process_sequence_id)
            ->where('id', '!=', $this->id)
            ->increment('order');
    }

    /**
     * Adjusts sequence order. This will decrement all `order` starting from the `order` number conflicting the
     * current model's `order` number. This is typically used when adding new sequence in between of two sequences.
     *
     * @return void
     */
    public function decrementOtherSequencesOrder()
    {
        self::where('order', '>=', $this->order)
            ->where('process_sequence_id', $this->process_sequence_id)
            ->where('id', '!=', $this->id)
            ->decrement('order');
    }

    /**
     * Checks if there is another sequence existing with the same `order` number in the workflow's sequences.
     * This method is typically used when adding a new sequence in between of two sequences.
     *
     * @return bool
     * */
    public function checkForSameOrder()
    {
        return self::where(['order' => $this->order, 'process_sequence_id' => $this->process_sequence_id])
            ->where('id', '!=', $this->id)
            ->exists();
    }

    /********************
    * R E L A T I O N S *
    ********************/

    /**
     * Get the previous step
     *
     * @return BelongsTo
     */
    public function previousStep()
    {
        return $this->belongsTo(self::class, 'previous_step_id');
    }

    /**
     * Get the next step
     *
     * @return BelongsTo
     */
    public function nextStep()
    {
        return $this->belongsTo(self::class, 'next_step_id');
    }

    /**
     * Get the parent process sequence of this process sequence link
     *
     * @return BelongsTo
     */
    public function processSequence()
    {
        return $this->belongsTo(ProcessSequence::class);
    }

    /**
     * Get the process of this process sequence link
     *
     * @return BelongsTo
     */
    public function process()
    {
        return $this->belongsTo(Process::class);
    }

    /********************************
     * E N D  O F  R E L A T I O N S *
     ********************************/

    /**************
    * S C O P E S *
    **************/

    /**
     * Add condition to order sequence steps based on the their order no
     *
     * @param Builder
     *
     * @return Builder
     */
    public function scopeSortedSteps(Builder $query): Builder
    {
        $query->orderBy('order', 'ASC');

        return $query;
    }

    /**************************
    * E N D  O F  S C O P E S *
    **************************/
}
