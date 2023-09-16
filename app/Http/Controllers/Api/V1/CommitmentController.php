<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Commitment;
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
}
