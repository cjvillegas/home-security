<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    /**
     * This will be the employee's index page. After a successful authentication
     * and no referrer route, the employee will be redirect directly here.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('employee.index');
    }

    /**
     * Get an employee based on the provided barcode
     * The request will throw a 404 if no employee found
     *
     * @return JsonResponse
     */
    public function getEmployeeByBarcode(Request $request)
    {
        return response()->json(Employee::where('barcode', $request->barcode)->firstOrFail());
    }
}
