<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\VendorInventory;
use App\Models\VendorPayment;
use App\Models\VendorPeriod;
use Illuminate\Http\Request;

class VendorPaymentController extends Controller
{
    public function byPeriod($period_id, Request $request)
    {
        $user = $request->user();
        $query = VendorPayment::where('period_id', $period_id);

        if ($user->role_id == 2) {
            $query->where('vendor_id', $user->id);
        }

        $payments = $query->orderBy('date')->get();
        return response()->json(['body' => $payments]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if ($user->role_id != 1) {
            return response()->json(['body' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'vendor_id' => 'required|integer|exists:users,id',
            'period_id' => 'required|integer|exists:vendor_periods,id',
            'amount'    => 'required|numeric|min:0.01',
            'date'      => 'required|date',
            'notes'     => 'nullable|string|max:500',
        ]);

        $payment = VendorPayment::create($data);
        return response()->json(['body' => $payment], 201);
    }

    public function balance($period_id, Request $request)
    {
        $user = $request->user();
        $period = VendorPeriod::with('vendor')->findOrFail($period_id);

        if ($user->role_id == 2 && $user->id != $period->vendor_id) {
            return response()->json(['body' => 'Forbidden'], 403);
        }

        $assignedValue = VendorInventory::where('period_id', $period_id)
            ->with('articleSize')
            ->get()
            ->sum(fn($inv) => $inv->quantity_assigned * ($inv->articleSize->sale_price ?? 0));

        $totalPaid = VendorPayment::where('period_id', $period_id)->sum('amount');

        $payments = VendorPayment::where('period_id', $period_id)
            ->orderBy('date')
            ->get();

        return response()->json([
            'body' => [
                'total_assigned_value' => $assignedValue,
                'total_paid'           => $totalPaid,
                'balance'              => $assignedValue - $totalPaid,
                'status'               => $period->status,
                'payments'             => $payments,
            ]
        ]);
    }
}
