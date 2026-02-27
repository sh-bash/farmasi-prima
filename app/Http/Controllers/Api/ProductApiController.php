<?php

namespace App\Http\Controllers\Api;

use App\Helpers\KNNHelper;
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

    public function saleFind(Request $request)
    {
        $search = $request->q;

        /* ========================================
        PRELOAD SINGLE PRODUCT (EDIT MODE)
        ======================================== */
        if ($request->id) {

            $product = Product::find($request->id);

            if (!$product) {
                return response()->json(['results' => []]);
            }

            return response()->json([
                'results' => [[
                    'id'    => $product->id,
                    'text'  => $product->name . ' (' . $product->barcode . ')',
                    'price' => $product->het,
                ]]
            ]);
        }

        /* ========================================
        NORMAL SEARCH
        ======================================== */
        $products = Product::query()
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('barcode', 'like', "%{$search}%");
                });
            })
            ->limit(20)
            ->get();

        /* ============================
        AMBIL FIRST SEBELUM MAP
        ============================ */
        $recommendFrom = null;

        if ($products->isNotEmpty()) {
            $recommendFrom = $products->first()->id;
        }

        $results = $products->map(function ($product) {
            return [
                'id'    => $product->id,
                'text'  => $product->name . ' (' . $product->barcode . ')',
                'price' => $product->het,
            ];
        })->toArray();

        /* ========================================
        KNN RECOMMENDATION
        ======================================== */
        if ($recommendFrom) {

            $recommendations = KNNHelper::findSubstitutes($recommendFrom, 3);

            foreach ($recommendations as $recProduct) {

                // Hindari duplikat jika sudah ada di search
                if (!collect($results)->pluck('id')->contains($recProduct->id)) {

                    $results[] = [
                        'id'    => $recProduct->id,
                        'text'  => '⭐ ' . $recProduct->name .
                                ' (' . $recProduct->barcode . ') - Rekomendasi',
                        'price' => $recProduct->het,
                        'is_recommendation' => true
                    ];
                }
            }
        }

        return response()->json([
            'results' => $results
        ]);
    }
}