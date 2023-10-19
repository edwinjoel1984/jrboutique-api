<?php


namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\GroupSizeResource;
use App\Models\ArticleSize;
use App\Models\GroupSize;
use DNS2D;
use DNS1D;

class GroupSizeController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show(GroupSize $groupSize)
    {

        return (new GroupSizeResource($groupSize));
    }

    public function generate_qrcode()
    {
        // dd(Str::orderedUuid()->toString());
        $number = 1;
        dd(ArticleSize::where('uniquecode', '=', $number)->exists());
        // return DNS1D::getBarcodeHTML('4445645656', 'PHARMA2T');
        // return QrCode::size(300)->backgroundColor(253, 90, 181)->generate("Edwin", './articles/qr.svg');
    }
}
