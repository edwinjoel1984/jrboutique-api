<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ArticleSize;
use App\Models\OrderDetail;
use App\Models\VendorInventory;
use App\Models\VendorPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendorInventoryController extends Controller
{
    public function byPeriod($period_id, Request $request)
    {
        $user = $request->user();
        $query = VendorInventory::with(['articleSize', 'articleSize.article', 'articleSize.size'])
            ->where('period_id', $period_id);

        if ($user->role_id == 2) {
            $query->where('vendor_id', $user->id);
        }

        $period = VendorPeriod::findOrFail($period_id);
        $items = $query->get()->map(function ($item) use ($period) {
            $isCustom = is_null($item->article_size_id);

            $soldQty = 0;
            if (!$isCustom) {
                $soldQty = OrderDetail::whereHas('order', function ($q) use ($item, $period) {
                    $q->where('seller_id', $item->vendor_id)
                      ->where('status', 'CLOSED')
                      ->whereYear('order_date', $period->year)
                      ->whereMonth('order_date', $period->month);
                })->where('article_size_id', $item->article_size_id)->sum('quantity');
            }

            $available = $item->quantity_assigned - $item->quantity_returned - $soldQty;

            $articleSizeData = $isCustom
                ? [
                    'id'      => null,
                    'price'   => (float) $item->custom_price,
                    'Article' => ['name' => $item->custom_name, 'ref' => ''],
                    'Size'    => ['name' => ''],
                ]
                : [
                    'id'      => $item->articleSize->id,
                    'price'   => $item->articleSize->sale_price,
                    'Article' => [
                        'name' => $item->articleSize->article->name ?? '',
                        'ref'  => $item->articleSize->article->ref ?? '',
                    ],
                    'Size' => [
                        'name' => $item->articleSize->size->name ?? '',
                    ],
                ];

            return [
                'id'                 => $item->id,
                'vendor_id'          => $item->vendor_id,
                'period_id'          => $item->period_id,
                'article_size_id'    => $item->article_size_id,
                'is_custom'          => $isCustom,
                'custom_name'        => $item->custom_name,
                'custom_price'       => $item->custom_price,
                'quantity_assigned'  => $item->quantity_assigned,
                'quantity_returned'  => $item->quantity_returned,
                'quantity_sold'      => $soldQty,
                'quantity_available' => max(0, $available),
                'status'             => $item->status,
                'ArticleSize'        => $articleSizeData,
            ];
        });

        return response()->json(['body' => $items]);
    }

    public function assign(Request $request)
    {
        $user = $request->user();
        if ($user->role_id != 1) {
            return response()->json(['body' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'period_id'                   => 'required|integer|exists:vendor_periods,id',
            'items'                       => 'required|array|min:1',
            'items.*.article_size_id'     => 'nullable|integer|exists:article_sizes,id',
            'items.*.custom_name'         => 'nullable|string|max:255',
            'items.*.custom_price'        => 'nullable|numeric|min:0',
            'items.*.quantity'            => 'required|integer|min:1',
        ]);

        $period = VendorPeriod::findOrFail($data['period_id']);

        DB::beginTransaction();
        try {
            foreach ($data['items'] as $item) {
                $isCustom = empty($item['article_size_id']);

                if ($isCustom) {
                    if (empty($item['custom_name']) || !isset($item['custom_price'])) {
                        DB::rollBack();
                        return response()->json(['body' => 'Los artículos personalizados requieren nombre y precio'], 422);
                    }

                    VendorInventory::create([
                        'vendor_id'         => $period->vendor_id,
                        'period_id'         => $data['period_id'],
                        'article_size_id'   => null,
                        'custom_name'       => $item['custom_name'],
                        'custom_price'      => $item['custom_price'],
                        'quantity_assigned' => $item['quantity'],
                        'quantity_returned' => 0,
                    ]);
                } else {
                    $articleSize = ArticleSize::lockForUpdate()->findOrFail($item['article_size_id']);

                    if ($articleSize->quantity < $item['quantity']) {
                        DB::rollBack();
                        return response()->json(['body' => "Stock insuficiente para el artículo #{$item['article_size_id']}"], 422);
                    }

                    $articleSize->quantity -= $item['quantity'];
                    $articleSize->save();

                    $existing = VendorInventory::where('period_id', $data['period_id'])
                        ->where('article_size_id', $item['article_size_id'])
                        ->where('vendor_id', $period->vendor_id)
                        ->first();

                    if ($existing) {
                        $existing->quantity_assigned += $item['quantity'];
                        $existing->save();
                    } else {
                        VendorInventory::create([
                            'vendor_id'         => $period->vendor_id,
                            'period_id'         => $data['period_id'],
                            'article_size_id'   => $item['article_size_id'],
                            'quantity_assigned' => $item['quantity'],
                            'quantity_returned' => 0,
                        ]);
                    }
                }
            }

            DB::commit();
            return response()->json(['body' => 'Prendas asignadas correctamente'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['body' => $e->getMessage()], 500);
        }
    }

    public function returnProducts(Request $request)
    {
        $user = $request->user();
        if ($user->role_id != 1) {
            return response()->json(['body' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'items'                          => 'required|array|min:1',
            'items.*.vendor_inventory_id'    => 'required|integer|exists:vendor_inventories,id',
            'items.*.quantity'               => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            foreach ($data['items'] as $item) {
                $inv = VendorInventory::lockForUpdate()->findOrFail($item['vendor_inventory_id']);
                $available = $inv->quantity_assigned - $inv->quantity_returned;

                if ($item['quantity'] > $available) {
                    DB::rollBack();
                    return response()->json(['body' => "Cantidad a devolver supera el disponible para inventario #{$item['vendor_inventory_id']}"], 422);
                }

                $inv->quantity_returned += $item['quantity'];

                if ($inv->quantity_returned >= $inv->quantity_assigned) {
                    $inv->status = 'FULLY_RETURNED';
                }
                $inv->save();

                // Only restore stock for catalog articles — custom items have no stock record
                if (!is_null($inv->article_size_id)) {
                    $articleSize = ArticleSize::findOrFail($inv->article_size_id);
                    $articleSize->quantity += $item['quantity'];
                    $articleSize->save();
                }
            }

            DB::commit();
            return response()->json(['body' => 'Devolución registrada correctamente']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['body' => $e->getMessage()], 500);
        }
    }
}
