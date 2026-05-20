<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\VendorPeriod;
use Illuminate\Http\Request;

class VendorPeriodController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = VendorPeriod::with('vendor');

        if ($user->role_id == 2) {
            $query->where('vendor_id', $user->id);
        } elseif ($request->has('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        $periods = $query->orderByDesc('year')->orderByDesc('month')->get();

        $periods = $periods->map(function ($period) {
            $totalAssigned = \App\Models\VendorInventory::where('period_id', $period->id)
                ->join('article_sizes', 'article_sizes.id', '=', 'vendor_inventories.article_size_id')
                ->selectRaw('SUM(vendor_inventories.quantity_assigned * article_sizes.sale_price) as total')
                ->value('total') ?? 0;

            $totalPaid = \App\Models\VendorPayment::where('period_id', $period->id)->sum('amount');

            return array_merge($period->toArray(), [
                'total_assigned_value' => (float) $totalAssigned,
                'total_paid'           => (float) $totalPaid,
                'balance'              => (float) $totalAssigned - (float) $totalPaid,
            ]);
        });

        return response()->json(['body' => $periods]);
    }

    public function show($id)
    {
        $period = VendorPeriod::with('vendor')->findOrFail($id);
        return response()->json(['body' => $period]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if ($user->role_id != 1) {
            return response()->json(['body' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'vendor_id' => 'required|integer|exists:users,id',
            'year'      => 'required|integer|min:2020|max:2100',
            'month'     => 'required|integer|min:1|max:12',
        ]);

        $exists = VendorPeriod::where('vendor_id', $data['vendor_id'])
            ->where('year', $data['year'])
            ->where('month', $data['month'])
            ->exists();

        if ($exists) {
            return response()->json(['body' => 'Ya existe un periodo para ese mes y vendedor'], 422);
        }

        $period = VendorPeriod::create($data);
        return response()->json(['body' => $period], 201);
    }

    public function close($id, Request $request)
    {
        $user = $request->user();
        if ($user->role_id != 1) {
            return response()->json(['body' => 'Forbidden'], 403);
        }

        $period = VendorPeriod::findOrFail($id);

        $totalAssigned = \App\Models\VendorInventory::where('period_id', $period->id)
            ->join('article_sizes', 'article_sizes.id', '=', 'vendor_inventories.article_size_id')
            ->selectRaw('SUM(vendor_inventories.quantity_assigned * article_sizes.sale_price) as total')
            ->value('total') ?? 0;

        $totalPaid = \App\Models\VendorPayment::where('period_id', $period->id)->sum('amount');

        $balance = (float) $totalAssigned - (float) $totalPaid;

        if ($balance > 0) {
            $fmt = '$' . number_format($balance, 0, ',', '.');
            return response()->json(['body' => "No se puede cerrar el periodo: saldo pendiente de {$fmt}"], 422);
        }

        $period->status = 'CLOSED';
        $period->save();

        return response()->json(['body' => $period]);
    }
}
