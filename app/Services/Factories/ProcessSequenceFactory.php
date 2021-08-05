<?php
namespace App\Services\Factories;

use App\Models\ProcessSequence\ProcessSequence;
use App\Models\ProcessSequence\ProcessSequenceLink;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ProcessSequenceFactory
{
    /**
     * Create new ProcessSequenceLink
     *
     * @param Request $request
     *
     * @return ProcessSequenceLink
     */
    public function createNewStep(Request $request): ProcessSequenceLink
    {
        $processSequenceLink = new ProcessSequenceLink();
        $processSequenceLink->process_id = $request->get('process_id');
        $processSequenceLink->process_sequence_id = $request->get('process_sequence_id');
        $processSequenceLink->previous_step_id = $request->get('previous_step_id', null);
        $processSequenceLink->order = 1;
        $processSequenceLink->save();

        // update the order of the current step if the step being added has previous step
        if ($processSequenceLink->previousStep) {
            $processSequenceLink->order = $processSequenceLink->previousStep->order + 1;
            $processSequenceLink->save();

            // adjusts the sequences' order
            if ($processSequenceLink->checkForSameOrder()) {
                $processSequenceLink->incrementOtherSequencesOrder();
            }
        }

        // build the sequence's steps
        $this->buildSequenceLinkList($processSequenceLink->processSequence);

        $link = $processSequenceLink->refresh();

        return $link;
    }

    /**
     * Adjusts the steps ordering. This will cater either up or down directions
     *
     * @param ProcessSequenceLink $processSequenceLink
     * @param mixed $direction
     *
     * @return Collection
     */
    public function adjustStepsOrder(ProcessSequenceLink $processSequenceLink, string $direction = null): Collection
    {
        // if the direction is down, increment the current step
        if ($direction === 'down') {
            $this->adjustOrderDown($processSequenceLink);
        }

        // if the direction is down, decrement the current step
        if ($direction === 'up') {
            $this->adjustOrderUp($processSequenceLink);
        }

        // build the sequence's steps
        return $this->buildSequenceLinkList($processSequenceLink->processSequence);
    }

    /**
     * Builds the sequence steps. This method will ensure that the sequence will be in proper order
     *
     * @param ProcessSequence $processSequence
     *
     * @return Collection
     */
    public function buildSequenceLinkList(ProcessSequence $processSequence): Collection
    {
        $steps = $processSequence->steps()->sortedSteps()->get();

        $steps->each(function ($step, $key) use ($steps) {
            $prevStep = $steps->first(function ($item, $index) use ($key) {
                return $index === ($key - 1);
            });
            $currentStep = $step;
            $nextStep = $steps->first(function ($item, $index) use ($key) {
                return $index === ($key + 1);
            });

            $currentStep->order = $key + 1;
            $currentStep->previous_step_id = optional($prevStep)->id;
            $currentStep->next_step_id = optional($nextStep)->id;

            $currentStep->save();
        });

        return $processSequence->steps()->sortedSteps()->get();
    }

    /**
     * Adjusts the sequence steps order in up direction.
     * Up direction means the current step's order will decrement by one and the subsequent
     * steps affected by the change will increment to one
     *
     * @param ProcessSequenceLink
     *
     * @return void
     */
    private function adjustOrderUp(ProcessSequenceLink $processSequenceLink): void
    {
        $processSequenceLink->order--;

        // checks if the current step has previous step
        // if so, decrement that next step so it would look like they just swapped places
        if ($processSequenceLink->previousStep) {
            $processSequenceLink->previousStep->order++;
            $processSequenceLink->previousStep->save();
        }
        $processSequenceLink->save();

        // increment the order of other steps
        $processSequenceLink->incrementOtherSequencesOrder();
    }

    /**
     * Adjusts the sequence steps order in down direction.
     * Down direction means the current step's order will increment by one and the subsequent
     * steps affected by the change will decrement to one
     *
     * @param ProcessSequenceLink
     *
     * @return void
     */
    private function adjustOrderDown(ProcessSequenceLink $processSequenceLink): void
    {
        $processSequenceLink->order++;

        // checks if the current step has next step
        // if so, decrement that next step so it would look like they just swapped places
        if ($processSequenceLink->nextStep) {
            $processSequenceLink->nextStep->order--;
            $processSequenceLink->nextStep->save();
        }

        $processSequenceLink->save();

        // decrement the order of other steps
        $processSequenceLink->decrementOtherSequencesOrder();
    }
}
