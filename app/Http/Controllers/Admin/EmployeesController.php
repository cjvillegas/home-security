<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyEmployeeRequest;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Employee;
use App\Models\Shift;
use App\Models\Team;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EmployeesController extends Controller
{
    use CsvImportTrait;

    public function index()
    {
        abort_if(Gate::denies('employee_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $employees = Employee::with(['shift', 'team'])->get();

        return view('admin.employees.index', compact('employees'));
    }

    public function fetchEmployees()
    {
        $employees = Employee::all();

        return response()->json(['employees' => $employees], 200);
    }

    public function create()
    {
        abort_if(Gate::denies('employee_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $shifts = Shift::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $teams = Team::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.employees.create', compact('shifts', 'teams'));
    }

    public function store(StoreEmployeeRequest $request)
    {
        $attributes = $request->all();
        $attributes['barcode'] = (new Employee())->generateBarcode();

        Employee::create($attributes);

        return redirect()->route('admin.employees.index');
    }

    public function edit(Employee $employee)
    {
        abort_if(Gate::denies('employee_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $shifts = Shift::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $teams = Team::all()->pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $employee->load('shift', 'team');

        return view('admin.employees.edit', compact('shifts', 'teams', 'employee'));
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        $employee->update($request->all());

        return redirect()->route('admin.employees.index');
    }

    public function show(Employee $employee)
    {
        abort_if(Gate::denies('employee_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $employee->load('shift', 'team');

        return view('admin.employees.show', compact('employee'));
    }

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
            $employee->save();
        }

        return back();
    }

    public function massDestroy(MassDestroyEmployeeRequest $request)
    {
        Employee::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function printBarcode(Employee $employee)
    {
        return view('admin.employees.print-barcode')->with('employee', $employee);;
    }
}
