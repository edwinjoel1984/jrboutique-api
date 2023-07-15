<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Validator;
use Illuminate\Http\Request;

use App\Http\Resources\V1\BrandResource;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $brands = Brand::all();
        return $this->sendResponse(BrandResource::collection($brands), 'Brands retrieved successfully.');
        //   return BrandResource::collection(Brand::latest()->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
            'description' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $brand = Brand::create($input);

        return $this->sendResponse(new BrandResource($brand), 'Brand created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $brand = Brand::find($id);

        if (is_null($brand)) {
            return $this->sendError('Brand not found.');
        }

        return $this->sendResponse(new BrandResource($brand), 'Brand retrieved successfully.');
        // return new BrandResource($brand);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Brand $brand)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required',
            'description' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $brand->name = $input['name'];
        $brand->description = $input['description'];
        $brand->save();

        return $this->sendResponse(new BrandResource($brand), 'Brand updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $brand = Brand::find($id);

        if (is_null($brand)) {
            return $this->sendError('Brand not found.');
        }
        $brand->delete();
        return $this->sendResponse([], 'Brand deleted successfully.');
    }
}
