<?php

namespace App\Http\Controllers\Admin\OvertimeBooking;

use App\Http\Controllers\Controller;
use App\Models\OvertimeBooking;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function store(Request $request, OvertimeBooking $overtimeBooking)
    {
        DB::beginTransaction();
        try {
            $slot = $overtimeBooking->create($request->all());
            DB::commit();

            return response()->json([
                'message' => 'Successfully saved new Slot'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => "Something went wrong when creating a new Slot."], 500);
        }
    }
}
