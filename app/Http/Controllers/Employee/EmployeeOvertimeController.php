<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeOvertime;
use App\Models\OvertimeBooking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

    public function getEmployee(Request $request)
    {
        $employee = Employee::
            where('barcode', $request->barcode)->with('confirmedSlots')
            ->with('overtimeSlots.checkedBy', 'overtimeSlots.overtimeBooking')
            ->first();

        if (is_null($employee)) {
            return response()->json([
                'message' => 'No Employee Found'
            ], 201);
        }
        return response()->json([
            'employee' => $employee
        ]);
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
            ->get();

        return response()->json([
            'slots' => $slots,
        ]);
    }

    /**
     * Store selected Overtime Slots.
     *
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $slots = $request->selectedSlots;
        $barcode = $request->barcode;

        $employee = Employee::where('barcode', $barcode)->first();

        DB::beginTransaction();
        try {

            foreach ($slots as $slot) {
                $employee->overtimeSlots()->create([
                    'overtime_booking_id' => $slot['id'],
                    'employee_id' => $barcode,
                ]);
            }

            DB::commit();
            return response()->json([
                'message' => 'Sucessfully submitted.'
            ]);

        } catch (\Exception $error) {
            DB::rollBack();
            Log::info($error);
            return response()->json([
                'message' => 'Error occured while processing your Overtime Booking Slot.',
                'error' => $error
            ], 500);
        }

    }
}
