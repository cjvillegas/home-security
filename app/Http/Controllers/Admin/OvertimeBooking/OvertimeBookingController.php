<?php

namespace App\Http\Controllers\Admin\OvertimeBooking;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeOvertime;
use App\Models\OvertimeBooking;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $size = $request->get('size');

        $dateRange = $request->dateRange;

        $slots = OvertimeBooking::
            orderBy('available_date', 'DESC')
            ->when($dateRange, function ($query) use ($dateRange) {
                $query->whereBetween('available_date', $dateRange);
            });

        $slots = $slots->paginate($size);

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

    /**
     * Employee Overtime Page
     *
     * @return void
     */
    public function employeesOvertime()
    {
        return view('admin.overtime-booking.employee');
    }

    /**
     * Get lists of Employee's overtime
     *
     * @param  mixed $request
     *
     * @return JsonResponse
     */
    public function getOvertimeConfirmations(Request $request)
    {
        $dateRange = $request->dateRange ?? [];

        $employeeOvertimes = EmployeeOvertime::select([
                'employees.fullname',
                'employees.id',
                DB::raw('COUNT(employee_overtimes.id) AS confirmed_slots'),
                DB::raw('SUM(overtime_bookings.working_hours) AS total_confirmed_hours'),
                'overtime_bookings.available_date'
            ])
            ->join('employees', 'employees.id', 'employee_id')
            ->join('overtime_bookings', 'overtime_bookings.id', 'overtime_booking_id')

            ->groupBy('employees.fullname');

        if (isset($request->dateRange)) {
             $employeeOvertimes->whereBetween('overtime_bookings.available_date', $dateRange);
        }

        $employeeOvertimes = $employeeOvertimes->get();
        foreach ($employeeOvertimes as $overtime) {
            $approvedHours = OvertimeBooking::select([DB::raw('SUM(working_hours) AS approved_hours')])
                ->join('employee_overtimes', 'employee_overtimes.overtime_booking_id', 'overtime_bookings.id')
                ->where('employee_id', $overtime->id)
                ->whereBetween('overtime_bookings.available_date', $dateRange)
                ->where('is_approved', true)
                ->first();
            $availableSlots = OvertimeBooking::select([DB::raw('COUNT(overtime_bookings.id) AS available_slots')])
                ->join('employee_overtimes', 'employee_overtimes.overtime_booking_id', 'overtime_bookings.id')
                ->whereDate('overtime_bookings.available_date', Carbon::parse($overtime->available_date)->format('Y-m-d'))
                ->where('is_approved', true)
                ->first();

            $overtime->approved_hours = $approvedHours->approved_hours ?? 0;
            $overtime->available_slots  = $availableSlots->available_slots ?? 0;
         }

        return response()->json([
            'confirmations' => $employeeOvertimes
        ]);
    }

    public function showEmployeeOvertimeRequests(Request $request)
    {
        $employee = Employee::where('id', $request->employeeId)
            ->with('overtimeSlots.checkedBy', 'overtimeSlots.overtimeBooking')
            ->first();
        return response()->json([
            'employee' => $employee
        ]);
    }

    /**
     * Manual Entry of Employee Overtime
     *
     * @param  mixed $request
     *
     * @return JsonResponse
     */
    public function manualEmployeeOvertime(Request $request)
    {
        $confirmations = EmployeeOvertime::create($request->all());

        return response()->json([
            'confirmations' => $confirmations
        ]);
    }

    /**
     * Return only those slots that are available and not locked
     *
     * @return void
     */
    public function getAllSlots()
    {
        $availableSlots = OvertimeBooking::
            select(['id', 'available_date', 'working_hours'])
            ->where('is_locked', false)
            ->whereDate('available_date', '>=', Carbon::now()->format('Y-m-d'))
            ->get();

        return response()->json([
            'availableSlots' => $availableSlots
        ]);
    }

    /**
     * Save Manual Entry for Overtime
     *
     * @param  mixed $request
     *
     * @return void
     */
    public function saveEmployeeOvertime(Request $request)
    {
        DB::beginTransaction();
        EmployeeOvertime::create([
            'overtime_booking_id' => $request->overtime_booking_id,
            'employee_id' => $request->employee_id,
            'is_approved' => true,
            'approved_at' => Carbon::now(),
            'department' => $request->department,
            'shift' => $request->shift
        ]);

        DB::commit();
        return response()->json([
            'message' => 'Employee Overtime has been successfully created.'
        ]);
    }


    /**
     * Overtime Requests Page
     *
     * @return void
     */
    public function overtimeRequests()
    {
        return view('admin.overtime-booking.requests');
    }

    /**
     * Get Employee Overtime Requests
     *
     * @return JsonResponse
     */
    public function getEmployeeOvertimeRequests(Request $request)
    {
        $overtimeRequests = EmployeeOvertime::select([
            'employee_overtimes.id',
            'employees.barcode',
            'employees.fullname',
            'department',
            'shift',
            'available_date',
            'is_approved'
        ])
        ->join('overtime_bookings AS ob', 'ob.id', 'employee_overtimes.overtime_booking_id')
        ->join('employees', 'employees.id', 'employee_overtimes.employee_id')
        ->whereBetween('ob.available_date', $request->dateRange)
        ->orderBy('ob.available_date', 'DESC')
        ->get();

        return response()->json([
            'requests' => $overtimeRequests
        ]);
    }

    /**
     * Update all selected Employee Overtime Requests
     *
     * @param  mixed $request
     *
     * @return JsonResponse
     */
    public function updateEmployeeOvertimeRequests(Request $request)
    {
        $overtimeRequests = $request->overtimeRequests;
        $attribute = $request->value;
        DB::beginTransaction();
        try {
            foreach ($overtimeRequests as $overtimeRequest) {
                $overtimeRequestModel = EmployeeOvertime::find($overtimeRequest['id']);

                $overtimeRequestModel->is_approved = $attribute['is_approved'] ?? null;
                $overtimeRequestModel->department = $attribute['department'] ?? null;
                $overtimeRequestModel->shift = $attribute['shift'] ?? null;

                if (isset($attribute['is_approved']) && $attribute['is_approved'] == true) {
                    $overtimeRequestModel->approved_at = Carbon::now();
                }

                if (isset($attribute['is_approved']) && $attribute['is_approved'] == false) {
                    $overtimeRequest->rejected_at = Carbon::now();
                }

                if (!is_null($attribute['overtime_booking_id'])) {
                    $overtimeRequestModel->overtime_booking_id = $attribute['overtime_booking_id'];
                }

                $overtimeRequestModel->checked_by = Auth::user()->id;

                $overtimeRequestModel->save();
            }

            DB::commit();
            return response()->json([
                'message' => 'Sucessfully Updated Employee Overtime Requests'
            ]);
        }
        catch (Exception $e) {
            DB::rollBack();
            Log::info($e);

            return response()->json([
                'message' => 'An error occured while updating Employee Overtime Requests'
            ]);
        }

    }

}
