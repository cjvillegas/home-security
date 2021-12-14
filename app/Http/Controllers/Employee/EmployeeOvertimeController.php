<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\OvertimeBooking;
use Carbon\Carbon;

class EmployeeOvertimeController extends Controller
{
    /**
     * This will be the employee's page for Overtime Booking Slot.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('employee.overtime.overtime-booking');
    }

    /**
     * Get all Available Slots for Overtime
     *
     * @param  mixed $request
     *
     * @return JsonResponse
     */
    public function getAvailableSlots()
    {
        $slots = OvertimeBooking::
            whereDate('available_date', '>', Carbon::now())
            ->where('is_locked', false)
            ->get();

        return response()->json([
            'slots' => $slots
        ]);
    }
}
