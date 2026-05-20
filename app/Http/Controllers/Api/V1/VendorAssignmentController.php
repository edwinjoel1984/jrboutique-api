<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\VendorAssignment;
use App\Models\VendorInventory;
use App\Models\VendorPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendorAssignmentController extends Controller
{
    public function byPeriod($period_id, Request $request)
    {
        $user = $request->user();
        $period = VendorPeriod::findOrFail($period_id);

        if ($user->role_id == 2 && $user->id != $period->vendor_id) {
            return response()->json(['body' => 'Forbidden'], 403);
        }

        $assignments = VendorAssignment::with([
                'customer',
                'vendorInventory.articleSize.article',
                'vendorInventory.articleSize.size',
            ])
            ->where('period_id', $period_id)
            ->get()
            ->map(fn($a) => [
                'id'                  => $a->id,
                'customer_id'         => $a->customer_id,
                'customer'            => $a->customer ? [
                    'id'         => $a->customer->id,
                    'first_name' => $a->customer->first_name,
                    'last_name'  => $a->customer->last_name,
                ] : null,
                'vendor_inventory_id' => $a->vendor_inventory_id,
                'quantity'            => $a->quantity,
                'unit_price'          => $a->unit_price,
                'article_name'        => $a->vendorInventory->articleSize->article->name ?? '',
                'article_ref'         => $a->vendorInventory->articleSize->article->ref ?? '',
                'size_name'           => $a->vendorInventory->articleSize->size->name ?? '',
            ]);

        return response()->json(['body' => $assignments]);
    }

    public function destroy($id, Request $request)
    {
        $user = $request->user();
        $assignment = VendorAssignment::findOrFail($id);
        $period = VendorPeriod::findOrFail($assignment->period_id);

        if ($user->role_id == 2 && $user->id != $period->vendor_id) {
            return response()->json(['body' => 'Forbidden'], 403);
        }

        if ($period->status !== 'OPEN') {
            return response()->json(['body' => 'No se puede modificar un periodo cerrado'], 422);
        }

        // Check deletion won't leave customer overpaid
        $totalAssignedAfter = VendorAssignment::where('period_id', $assignment->period_id)
            ->where('customer_id', $assignment->customer_id)
            ->where('id', '!=', $id)
            ->selectRaw('SUM(quantity * unit_price) as total')
            ->value('total') ?? 0;

        $totalPaid = \App\Models\VendorCustomerPayment::where('period_id', $assignment->period_id)
            ->where('customer_id', $assignment->customer_id)
            ->sum('amount');

        if ($totalPaid > $totalAssignedAfter) {
            return response()->json([
                'body' => 'No se puede eliminar: el cliente ya tiene abonos que superarían el nuevo total asignado'
            ], 422);
        }

        $assignment->delete();
        return response()->json(['body' => 'Asignación eliminada']);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'customer_id'                         => 'required|integer|exists:customers,id',
            'period_id'                           => 'required|integer|exists:vendor_periods,id',
            'items'                               => 'required|array|min:1',
            'items.*.vendor_inventory_id'         => 'required|integer|exists:vendor_inventories,id',
            'items.*.quantity'                    => 'required|integer|min:1',
        ]);

        $period = VendorPeriod::findOrFail($data['period_id']);

        if ($user->role_id == 2 && $user->id != $period->vendor_id) {
            return response()->json(['body' => 'Forbidden'], 403);
        }

        DB::beginTransaction();
        try {
            foreach ($data['items'] as $item) {
                $inv = VendorInventory::findOrFail($item['vendor_inventory_id']);

                if ($inv->period_id != $data['period_id'] || $inv->vendor_id != $period->vendor_id) {
                    DB::rollBack();
                    return response()->json(['body' => 'Inventario no pertenece a este periodo'], 422);
                }

                $alreadyAssigned = VendorAssignment::where('vendor_inventory_id', $inv->id)->sum('quantity');
                $available = $inv->quantity_assigned - $inv->quantity_returned - $alreadyAssigned;

                if ($item['quantity'] > $available) {
                    DB::rollBack();
                    return response()->json(['body' => "Stock disponible insuficiente: solo {$available} unidades disponibles"], 422);
                }

                VendorAssignment::create([
                    'vendor_id'          => $period->vendor_id,
                    'customer_id'        => $data['customer_id'],
                    'period_id'          => $data['period_id'],
                    'vendor_inventory_id' => $inv->id,
                    'quantity'           => $item['quantity'],
                    'unit_price'         => $inv->articleSize->sale_price,
                ]);
            }

            DB::commit();
            return response()->json(['body' => 'Prendas asignadas al cliente'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['body' => $e->getMessage()], 500);
        }
    }
}
