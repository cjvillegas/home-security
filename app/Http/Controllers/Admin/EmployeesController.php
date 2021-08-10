<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyEmployeeRequest;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Employee;
use App\Models\ProcessSequence\ProcessSequence;
use App\Models\Shift;
use App\Models\Team;
use Gate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class EmployeesController extends Controller
{
    use CsvImportTrait;

    public function index()
    {
        abort_if(Gate::denies('employee_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $employees = Employee::with(['shift', 'team'])->get();
        $teams = Team::get();
        $shifts = Shift::get();

        return view('admin.employees.index', compact('employees', 'teams', 'shifts'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreEmployeeRequest  $request
     *
     * @return JsonResponse
     */
    public function store(StoreEmployeeRequest $request)
    {
        $attributes = $request->all();
        $attributes['barcode'] = (new Employee())->generateBarcode();

        $employee = Employee::create($attributes);

        return response()->json($employee);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateEmployeeRequest  $request
     * @param  Employee  $employee
     *
     * @return JsonResponse
     */
    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        $employee->update($request->all());

        return response()->json($employee->refresh()->loadMissing('shift', 'team'));
    }

    /**
     * Fetch the specified resource.
     *
     * @param  Employee  $employee
     *
     * @return JsonResponse
     */
    public function show(Employee $employee)
    {
        abort_if(Gate::denies('employee_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $employee->load('shift', 'team');

        return response()->json($employee);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Employee $employee
     *
     * @return JsonResponse
     */
    public function destroy(Employee $employee)
    {
        abort_if(Gate::denies('employee_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($employee->delete()) {
            // modify the deleted clock_num so that it will not cause conflict
            // to the newly created process category.
            // This is useful because the code clock_num is unique in the DB level and we are only soft deleting
            // data in the employees table
            $now = now()->unix();
            $employee->clock_num = null;
            $employee->barcode = $employee->barcode . "_$now";
            $employee->save();

            return response()->json(true);
        }

        return response()->json(false);
    }

    public function massDestroy(MassDestroyEmployeeRequest $request)
    {
        Employee::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function printBarcode(Employee $employee)
    {
        return view('admin.employees.print-barcode')->with('employee', $employee);
    }

    /**
     * Search Employee API
     *
     * @return JsonResponse
     */
    public function searchEmployee()
    {
        $searchString = request()->input('searchString');

        $employees = DB::table('employees')
            ->where('fullname', 'like', "%$searchString%")->take(15)->get();

        return response()->json(['employees' => $employees]);
    }

    /**
     * Fetch list of process categories
     *
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function getList(Request $request)
    {
        $searchString = $request->get('searchString');
        $status = $request->get('status');
        $size = $request->get('size');

        $employees = Employee::
            orderBy('created_at', 'desc')
            ->with([
                'shift' => function ($query) {
                    $query->select(['id', 'name']);
                },
                'team' => function ($query) {
                    $query->select('id', 'name', 'target');
                }
            ])
            ->when($searchString, function ($query) use ($searchString) {
                $query->where('fullname', 'like', "%{$searchString}%");
                $query->orWhere('barcode', 'like', "%{$searchString}%");
                $query->orWhere('pin_code', 'like', "%{$searchString}%");
            })
            ->when($status !== null, function ($query) use ($status) {
                $query->where('employees.is_active', $status);
            });

        $employees = $employees->paginate($size);

        return response()->json($employees);
    }

    /**
     * Fetch the specified resource.
     *
     * @param  Employee  $employee
     *
     * @return JsonResponse
     */
    public function changeStatus(Employee $employee)
    {
        $employee->is_active = !$employee->is_active;
        $employee->save();

        return response()->json($employee);
    }
}
