<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;

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
}
