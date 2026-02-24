<?php

namespace App\Helpers;

use App\Models\Master\Product;

class KNNHelper
{
    /**
     * Cari produk substitusi dengan KNN
     */
    public static function findSubstitutes($productId, $k = 5)
    {
        $target = Product::with('ingredients')->findOrFail($productId);
        $targetIngredientIds = $target->ingredients->pluck('id');

        // Filter awal (aturan farmasi)
        $candidates = Product::with('ingredients')
                            ->where('id', '!=', $productId)
                            ->where('category_id', $target->category_id)
                            // ->where('form_id', $target->form_id)
                            ->whereHas('ingredients', function ($q) use ($targetIngredientIds) {
                                $q->whereIn('ingredients.id', $targetIngredientIds);
                            })
                            ->get();

        if ($candidates->isEmpty()) {
            return collect();
        }

        // Ambil semua ingredient unik untuk vector space
        $ingredientIds = self::getAllIngredientIds($target, $candidates);

        $targetVector = self::buildVector($target, $ingredientIds);

        $distances = [];

        foreach ($candidates as $candidate) {
            $vector = self::buildVector($candidate, $ingredientIds);
            $distance = self::euclideanDistance($targetVector, $vector);

            $distances[] = [
                'product' => $candidate,
                'distance' => $distance
            ];
        }

        // Urutkan dari yang paling dekat
        usort($distances, fn($a, $b) => $a['distance'] <=> $b['distance']);

        return collect($distances)
            ->take($k)
            ->pluck('product');
    }

    /**
     * Ambil semua ingredient unik
     */
    private static function getAllIngredientIds($target, $candidates)
    {
        $ids = $target->ingredients->pluck('id')->toArray();

        foreach ($candidates as $c) {
            $ids = array_merge($ids, $c->ingredients->pluck('id')->toArray());
        }

        return array_unique($ids);
    }

    /**
     * Build vector produk
     * Vector = [ingredient1_strength, ingredient2_strength, ..., price_scaled]
     */
    private static function buildVector($product, $ingredientIds)
    {
        $vector = [];

        foreach ($ingredientIds as $ingredientId) {
            $ingredient = $product->ingredients->firstWhere('id', $ingredientId);

            $vector[] = $ingredient
                ? (float) $ingredient->pivot->strength
                : 0;
        }

        // Tambahkan harga sebagai fitur tambahan
        $vector[] = $product->het / 1000; // scaling

        return $vector;
    }

    /**
     * Euclidean Distance
     */
    private static function euclideanDistance($a, $b)
    {
        $sum = 0;

        for ($i = 0; $i < count($a); $i++) {
            $sum += pow($a[$i] - $b[$i], 2);
        }

        return sqrt($sum);
    }
}