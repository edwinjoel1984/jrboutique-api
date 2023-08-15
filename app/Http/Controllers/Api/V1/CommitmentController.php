<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Commitment;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CommitmentResource;
use Illuminate\Http\Request;
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
            'customer_id' => 'required',
            'memo' => 'required',
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
