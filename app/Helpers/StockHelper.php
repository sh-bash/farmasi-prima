<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class StockHelper
{
    public static function getCurrentStock(int $productId): int
    {
        $stockIn = DB::table('purchase_details')
            ->where('product_id', $productId)
            ->sum('qty');

        $stockOut = DB::table('sale_details')
            ->where('product_id', $productId)
            ->sum('qty');

        return $stockIn - $stockOut;
    }

    // Untuk validasi penjualan
    public static function checkStockAvailable(int $productId, int $qty): bool
    {
        return self::getCurrentStock($productId) >= $qty;
    }
}