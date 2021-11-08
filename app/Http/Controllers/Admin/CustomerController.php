<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|Response
     */
    public function index()
    {
        //abort_if(Gate::denies('customer_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.customers.index');
    }

    /**
     * Fetch Customers
     *
     * @param  Request $request
     *
     * @return JsonResponse
     */
    public function fetchCustomers(Request $request)
    {
        $customers = Customer::all();

        return response()->json(['customers' => $customers]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store(CustomerRequest $request)
    {
        Customer::create($request->all());
        return response()->json(
            [
                'message' => 'Customer Succesfully created.'
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Customer  $customer
     * @param  CustomerRequest $request
     *
     * @return JsonResponse
     */
    public function update(Customer $customer, CustomerRequest $request)
    {
        DB::beginTransaction();
        try {
            $customer->update($request->all());
            DB::commit();
            return response()->json(['message' => 'Customer Successfully Saved']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e);
            return response()->json(['message' => "Something went wrong when creating a new machine."], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Customer $customer
     *
     * @return JsonResponse
     */
    public function destroy(Customer $customer)
    {
        $deleted = $customer->delete();

        if ($deleted) {
            // modify the deleted code so that it will not cause conflict
            // to the newly created customers.
            // This is useful because the code column is unique in the DB level, and we are only soft deleting
            // data in the customers table
            $now = now()->unix();
            $customer->code = "{$customer->code}_deleted_{$now}";
            $customer->save();

            DB::commit();
        }

        return response()->json(['message' => 'Successfully Deleted!']);
    }
}
