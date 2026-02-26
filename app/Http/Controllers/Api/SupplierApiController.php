<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\Supplier;

class SupplierApiController extends Controller
{
    public function purchaseFind(Request $request)
    {
        $search = $request->q;

        if ($request->id) {
            $supplier = Supplier::find($request->id);

            return response()->json([
                'results' => [[
                    'id' => $supplier->id,
                    'text' => $supplier->name
                ]]
            ]);
        }

        $suppliers = Supplier::query()
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            })
            ->limit(20)
            ->get();

        return response()->json([
            'results' => $suppliers->map(function ($supplier) {
                return [
                    'id' => $supplier->id,
                    'text' => $supplier->name . ' (' . $supplier->code . ')',
                ];
            })
        ]);
    }
}