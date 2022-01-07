<?php

namespace App\Http\Controllers\Admin\OvertimeBooking;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeOvertime;
use App\Models\OvertimeBooking;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class OvertimeBookingController extends Controller
{
    /**
     * View for Overtime Booking Slot
     *
     * @return View
     */
    public function index()
    {
        abort_if(Gate::denies('overtime_booking_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.overtime-booking.index');
    }

    public function getSlots(Request $request)
    {
        abort_if(Gate::denies('overtime_booking_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $size = $request->get('size');

        $dateRange = $request->dateRange;

        $slots = OvertimeBooking::
            when($dateRange, function ($query) use ($dateRange) {
                $query->whereBetween('available_date', $dateRange);
            })
            ->orderBy('id', 'DESC');

        $slots = $slots->paginate($size);

        return response()->json([
            'slots' => $slots
        ]);
    }

    /**
     * Store Overtime Booking Slot
     *
     * @param  request $request
     *
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $dates = $request->get('dates');

        DB::beginTransaction();
        try {
            $overtimeBookings =  [];

            //iterate all selected dates
            foreach ($dates as $date) {
                array_push($overtimeBookings, [
                    'available_date' => Carbon::parse($date),
                    'working_hours' => $request->working_hours,
                    'created_at' => Carbon::now(),
                ]);
            }

            OvertimeBooking::insert($overtimeBookings);
            DB::commit();

            return response()->json([
                'message' => 'Successfully saved new Slot'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => "Something went wrong when creating a new Slot."], 500);
        }
    }

    /**
     * Toggle Lock and Unlock
     *
     * @param  request $request
     *
     * @return JsonResponse
     */
    public function toggleSlot(Request $request, OvertimeBooking $overtimeBooking)
    {
        // This is to identify if slot is locked or not. If locked, unlocked. Vice versa.
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
     * Delete Selected Slot
     *
     * @param  request $overtimeBooking
     *
     * @return JsonResponse
     */
    public function deleteSlot(OvertimeBooking $overtimeBooking)
    {
        $overtimeBooking->delete();

        return response()->json([
            'message' => 'Successfully deleted Slot'
        ]);
    }

    /**
     * Employee Overtime Page
     *
     * @return View
     */
    public function employeesOvertime()
    {
        return view('admin.overtime-booking.employee');
    }

    /**
     * Get lists of Employee's overtime
     *
     * @param  Request $request
     *
     * @return JsonResponse
     */
    public function getOvertimeConfirmations(Request $request)
    {
        $dateRange = $request->get('dateRange', []);

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

        if (!empty($dateRange)) {
             $employeeOvertimes->whereBetween('overtime_bookings.available_date', $dateRange);
        }

        $employeeOvertimes = $employeeOvertimes->get();

        // Do this to compute a value for 'Approved Hours' and 'Available Slots'
        // That two columns needs to be present upon fetching
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

    /**
     * Show selected Employee Data
     *
     * @param  request $request
     *
     * @return JsonResponse
     */
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
     * @param  request $request
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
     * @return JsonResponse
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
     * @param  Request $request
     *
     * @return JsonResponse
     */
    public function saveEmployeeOvertime(Request $request)
    {
        abort_if(Gate::denies('overtime_booking_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

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
     * @return View
     */
    public function overtimeRequests()
    {
        return view('admin.overtime-booking.requests');
    }

    /**
     * Get Employee Overtime Requests
     *
     * @param  Request $request
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
     * @param  Request $request
     *
     * @return JsonResponse
     */
    public function updateEmployeeOvertimeRequests(Request $request)
    {
        abort_if(Gate::denies('overtime_booking_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

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

            return response()->json([
                'message' => 'An error occured while updating Employee Overtime Requests'
            ]);
        }

    }

}
