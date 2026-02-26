<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Master\Product;

class ProductApiController extends Controller
{
    public function purchaseFind(Request $request)
    {
        $search = $request->q;

        if ($request->id) {

            $product = Product::find($request->id);

            if (!$product) {
                return response()->json(['results' => []]);
            }

            return response()->json([
                'results' => [[
                    'id' => $product->id,
                    'text' => $product->name . ' (' . $product->barcode . ')',
                    'price' => $product->het
                ]]
            ]);
        }

        $products = Product::query()
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            })
            ->limit(20)
            ->get();

        return response()->json([
            'results' => $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'text' => $product->name . ' (' . $product->barcode . ')',
                    'price' => $product->het,
                ];
            })
        ]);
    }
}