<?php

namespace App\Http\Controllers\Api\V1;


use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CustomerResource;
use App\Models\Commitment;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\PaymentCommitment;
use Illuminate\Support\Facades\DB;
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

    public function commitments_by_user($customer_id)
    {
        $commitments = Customer::find($customer_id)->commitments()->where('pending_amount', '>', 0)->get();
        return $this->sendResponse($commitments, 'Commitments by user retrieved successfully.');
    }

    public function create_payment(Request $request, $customer_id)
    {
        DB::beginTransaction();
        try {
            $commitments = Customer::find($customer_id)->commitments()->where('pending_amount', '>', 0)->get();
            if ($commitments->count() == 0) {
                return $this->sendError('No commitments found', [], 404);
            }
            $pending_total_amount = 0;
            foreach ($commitments as $commitment) {
                $pending_total_amount += $commitment['pending_amount'];
            }
            if ($pending_total_amount < $request['amount']) {
                return $this->sendError('Max Commitment value is $' . $pending_total_amount, [], 422);
            }

            $payment = Payment::create(["date" => $request['date'], "amount" => $request['amount'], "customer_id" => $customer_id]);

            $balance = $request['amount'];
            $idxCommitment = 0;
            $totalCommitments = $commitments->count();

            while ($balance > 0 && $idxCommitment < $totalCommitments) {
                if ($commitments[$idxCommitment]['pending_amount'] <= $balance) {
                    $paymentCommitment = ["date" => date("Y-m-d"), "amount" => $commitments[$idxCommitment]['pending_amount'], "payment_id" => $payment['id'], "commitment_id" => $commitments[$idxCommitment]['id']];
                    PaymentCommitment::create($paymentCommitment);
                    Commitment::find($commitments[$idxCommitment]['id'])->update(['pending_amount' => 0]);
                    $balance -= $commitments[$idxCommitment]['pending_amount'];
                } else {
                    $paymentCommitmentData = ["date" => date("Y-m-d"), "amount" => $balance, "payment_id" => $payment['id'], "commitment_id" => $commitments[$idxCommitment]["id"]];
                    PaymentCommitment::create($paymentCommitmentData);
                    Commitment::find($commitments[$idxCommitment]['id'])->update(['pending_amount' =>  $commitments[$idxCommitment]['pending_amount'] - $balance]);
                    $balance = 0;
                    $idxCommitment = $idxCommitment < $commitments->count();
                }
                $idxCommitment++;
            }


            DB::commit();
            return $this->sendResponse(["pending_amount" => $pending_total_amount - $request['amount']], [], 'Transaction Successfully');
            //code...
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
        }
    }
}
