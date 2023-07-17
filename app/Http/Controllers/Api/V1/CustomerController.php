<?php

namespace App\Http\Controllers\Api\V1;


use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CustomerResource;
use Illuminate\Http\Request;
use App\Models\Customer;
use Validator;


class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $customers = Customer::all();
        return $this->sendResponse(CustomerResource::collection($customers), 'Customers retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $input = $request->all();
        $validator = Validator::make($input, [
            'first_name' => 'required',
            'last_name' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $customer = Customer::create($input);


        return $this->sendResponse(new CustomerResource($customer), 'Customer created successfully.');
    }

    public function show(Customer $customer)
    {
        return new CustomerResource($customer);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'first_name' => 'required',
            'last_name' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $customer->first_name = $input['first_name'];
        $customer->last_name = $input['last_name'];
        $customer->document = $input['document'];
        $customer->address = $input['address'];
        $customer->phone = $input['phone'];
        $customer->save();

        return $this->sendResponse(new CustomerResource($customer), 'Customer updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        if ($customer->delete()) {
            return response()->json(['message' => 'Success'], 204);
        }
        return response()->json(['message' => 'Not found'], 404);
    }
}
