<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Commitment;
use App\Models\PaymentCommitment;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CommitmentResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;


class CommitmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $commitments = Commitment::all();
        return $this->sendResponse(CommitmentResource::collection($commitments), 'Commitments retrieved successfully.');
    }
    /**
     * Display a listing of the resource.
     */
    public function commitments_grouped_by_user()
    {
        // $commitments = Commitment::all();
        $commitments = DB::table('commitments')
            ->join('customers', 'commitments.customer_id', '=', 'customers.id')
            ->select(DB::raw('CONCAT (first_name, " ",last_name) AS customer_name, customer_id, sum(pending_amount) as total_pending_amount'))
            ->groupBy('customer_id')
            ->orderBy('customer_id')
            ->havingRaw('sum(pending_amount) > ?', [0])
            ->get();
        return $this->sendResponse($commitments, 'Commitments retrieved successfully.');
    }
    public function commitments_grouped_by_user_general()
    {
        // $commitments = Commitment::all();
        $commitments = DB::table('commitments as c')
            ->join('customers as c2', 'c2.id', '=', 'c.customer_id')
            ->select(
                'c.customer_id',
                'c2.first_name',
                'c2.last_name',
                DB::raw('SUM(c.total_amount) as total_purchased'),
                DB::raw('SUM(c.pending_amount) as pending_amount')
            )
            ->groupBy('c.customer_id')
            ->orderByDesc('total_purchased') // Assuming "comprado" is the alias for the total_amount
            ->get();
        return $this->sendResponse($commitments, 'Commitments retrieved successfully.');
    }
    public function payments_to_commitments(Request $request, $commitmentId)
    {
        $payments = PaymentCommitment::join('payments as p', 'p.id', '=', 'payment_commitments.payment_id')
            ->where('payment_commitments.commitment_id', $commitmentId)
            ->select('payment_commitments.date', 'p.amount as payment', 'payment_commitments.amount as payment_to_commitment')
            ->get();
        return $this->sendResponse($payments, 'Commitments retrieved successfully.');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'date' => 'required',
            'total_amount' => 'required',
            'customer_id' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $input["pending_amount"] = $input['total_amount'];
        $commitment = Commitment::create($input);

        return $this->sendResponse(new CommitmentResource($commitment), 'Commitment created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Commitment $commitment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Commitment $commitment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Commitment $commitment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Commitment $commitment)
    {
        //
    }

    public function dashboard_data()
    {
        $total_pending_amount = Commitment::sum('pending_amount');
        // add commitments current month and year
        $current_month_commitments = Commitment::whereMonth('date', '=', date('m'))->whereYear('date', '=', date('Y'))->sum('total_amount');
        // add payments current month and year
        $current_month_payments = PaymentCommitment::whereMonth('date', '=', date('m'))->whereYear('date', '=', date('Y'))->sum('amount');
        //add sales this week
        $current_week_sales = Commitment::whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])->sum('total_amount');
        $response = [
            "all_pending_amount" => $total_pending_amount,
            "current_month_commitments" => $current_month_commitments,
            "current_month_payments" => $current_month_payments,
            "current_week_sales" => $current_week_sales
        ];
        return $this->sendResponse($response, "Dashboard data retrieved successfully.");
    }
    public function commitments_by_date(Request $request)
    {
        $input = $request->all();
        $commitments = DB::table('commitments')
            ->select(DB::raw('DAYOFMONTH(date) as day'), DB::raw('COALESCE(SUM(total_amount), 0) as total'))
            ->whereYear('date', '=', $input["year"])
            ->whereMonth('date', '=', $input["month"])
            ->groupBy('day')
            ->orderByDesc('day')
            ->get();

        return $this->sendResponse($commitments, "Commitments filtered successfully.");
    }
}
