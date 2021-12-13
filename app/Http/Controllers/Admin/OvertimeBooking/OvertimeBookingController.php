<?php

namespace App\Http\Controllers\Admin\OvertimeBooking;

use App\Http\Controllers\Controller;
use App\Models\OvertimeBooking;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OvertimeBookingController extends Controller
{
    /**
     * View for Overtime Booking Slot
     *
     * @return void
     */
    public function index()
    {
        return view('admin.overtime-booking.index');
    }

    public function getSlots(Request $request)
    {
        $dateRange = $request->dateRange;

        $slots = OvertimeBooking::
            orderBy('available_date', 'ASC')
            ->when($dateRange, function ($query) use ($dateRange) {
                $query->whereBetween('available_date', $dateRange);
            })->get();

        return response()->json([
            'slots' => $slots
        ]);
    }

    /**
     * Store Overtime Booking Slot
     *
     * @param  mixed $request
     *
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $dates = $request->dates;
        DB::beginTransaction();
        try {
            //iterate all selected dates
            foreach ($dates as $date) {
                OvertimeBooking::create([
                    'available_date' => Carbon::parse($date),
                    'working_hours' => $request->working_hours
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Successfully saved new Slot'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e);
            return response()->json(['message' => "Something went wrong when creating a new Slot."], 500);
        }
    }

    /**
     * Toggle Lock and Unlock
     *
     * @param  mixed $request
     *
     * @return JsonResponse
     */
    public function toggleSlot(Request $request, OvertimeBooking $overtimeBooking)
    {
        if ($overtimeBooking->is_locked) {
            $overtimeBooking->is_locked = false;
        } else {
            $overtimeBooking->is_locked = true;
        }
        $overtimeBooking->save();

        $message = $overtimeBooking->is_locked ? 'locked' : 'unlocked';
        return response()->json([
            'message' => 'Successfully '. $message. ' Slot'
        ]);
    }
}
