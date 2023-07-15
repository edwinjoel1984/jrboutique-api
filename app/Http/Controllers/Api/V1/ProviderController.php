<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use Illuminate\Http\Request;

use App\Http\Resources\V1\ProviderResource;

class ProviderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $providers = Provider::all();
        return $this->sendResponse(ProviderResource::collection($providers), 'Providers retrieved successfully.');
       
        // return ProviderResource::collection(Provider::latest()->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Provider $provider)
    {        
        return new ProviderResource($provider);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Provider $provider)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Provider $provider)
    {
        if($provider->delete()){
            return response()->json(['message'=>'Success'], 204);
        }
        return response()->json(['message'=>'Not found'], 404);
    }
}
