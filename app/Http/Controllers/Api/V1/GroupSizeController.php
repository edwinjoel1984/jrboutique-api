<?php


namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\GroupSizeResource;
use App\Models\GroupSize;
use Illuminate\Http\Request;

class GroupSizeController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show(GroupSize $groupSize)
    {
        return (new GroupSizeResource($groupSize));
    }
}
