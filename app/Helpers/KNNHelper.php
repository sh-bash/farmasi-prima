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

    public static function findSubstitutesWithDetail($productId, $k = 5)
    {
        $target = Product::with('ingredients')->findOrFail($productId);
        $targetIngredientIds = $target->ingredients->pluck('id');

        $candidates = Product::with('ingredients')
            ->where('id', '!=', $productId)
            ->where('category_id', $target->category_id)
            ->whereHas('ingredients', function ($q) use ($targetIngredientIds) {
                $q->whereIn('ingredients.id', $targetIngredientIds);
            })
            ->get();

        if ($candidates->isEmpty()) {
            return [];
        }

        $ingredientIds = self::getAllIngredientIds($target, $candidates);

        $results = [];

        foreach ($candidates as $candidate) {

            $calculation = [];
            $sum = 0;

            foreach ($ingredientIds as $ingredientId) {

                $ingredientName = optional($target->ingredients->firstWhere('id', $ingredientId))->name
                    ?? optional($candidate->ingredients->firstWhere('id', $ingredientId))->name;

                $targetValue = optional($target->ingredients->firstWhere('id', $ingredientId))->pivot->strength ?? 0;
                $candidateValue = optional($candidate->ingredients->firstWhere('id', $ingredientId))->pivot->strength ?? 0;

                $diff = $targetValue - $candidateValue;
                $square = pow($diff, 2);
                $sum += $square;

                $calculation[] = [
                    'ingredient' => $ingredientName,
                    'target' => $targetValue,
                    'candidate' => $candidateValue,
                    'diff' => $diff,
                    'square' => $square,
                ];
            }

            // Tambahkan harga
            $priceTarget = $target->het / 1000;
            $priceCandidate = $candidate->het / 1000;
            $priceDiff = $priceTarget - $priceCandidate;
            $priceSquare = pow($priceDiff, 2);
            $sum += $priceSquare;

            $calculation[] = [
                'ingredient' => 'PRICE (scaled)',
                'target' => $priceTarget,
                'candidate' => $priceCandidate,
                'diff' => $priceDiff,
                'square' => $priceSquare,
            ];

            $distance = sqrt($sum);

            $results[] = [
                'product' => $candidate,
                'calculation' => $calculation,
                'distance' => $distance
            ];
        }

        usort($results, fn($a, $b) => $a['distance'] <=> $b['distance']);

        return [
            'target_product' => $target,
            'results' => array_slice($results, 0, $k)
        ];
    }

    public static function findSubstitutesFullProcess($productId, $k = 5)
    {
        $log = [];

        // ========================
        // STEP 1 - TARGET
        // ========================
        $target = Product::with('ingredients')->findOrFail($productId);

        $log['step_1_target'] = [
            'id' => $target->id,
            'name' => $target->name,
            'ingredients' => $target->ingredients->map(function ($i) {
                return [
                    'name' => $i->name,
                    'strength' => $i->pivot->strength
                ];
            })
        ];

        // ========================
        // STEP 2 - CANDIDATES
        // ========================
        $targetIngredientIds = $target->ingredients->pluck('id');

        $candidates = Product::with('ingredients')
            ->where('id', '!=', $productId)
            ->where('category_id', $target->category_id)
            ->whereHas('ingredients', function ($q) use ($targetIngredientIds) {
                $q->whereIn('ingredients.id', $targetIngredientIds);
            })
            ->get();

        $log['step_2_candidates'] = $candidates->pluck('name');

        // ========================
        // STEP 3 - VECTOR SPACE
        // ========================
        $ingredientIds = self::getAllIngredientIds($target, $candidates);

        $ingredientNames = collect($ingredientIds)->map(function ($id) use ($target, $candidates) {
            return optional($target->ingredients->firstWhere('id', $id))->name
                ?? optional($candidates->first()->ingredients->firstWhere('id', $id))->name;
        });

        $log['step_3_vector_space'] = $ingredientNames;

        // ========================
        // STEP 4 - TARGET VECTOR
        // ========================
        $targetVector = self::buildVector($target, $ingredientIds);
        $log['step_4_target_vector'] = $targetVector;

        // ========================
        // STEP 5 & 6 - DISTANCE
        // ========================
        $distances = [];

        foreach ($candidates as $candidate) {

            $vector = self::buildVector($candidate, $ingredientIds);

            $calcDetail = [];
            $sum = 0;

            for ($i = 0; $i < count($targetVector); $i++) {
                $diff = $targetVector[$i] - $vector[$i];
                $square = pow($diff, 2);
                $sum += $square;

                $calcDetail[] = [
                    'feature' => $ingredientNames[$i] ?? 'price',
                    'target' => $targetVector[$i],
                    'candidate' => $vector[$i],
                    'diff' => $diff,
                    'square' => $square
                ];
            }

            $distance = sqrt($sum);

            $distances[] = [
                'product' => $candidate->name,
                'vector' => $vector,
                'calculation' => $calcDetail,
                'sum' => $sum,
                'distance' => $distance
            ];
        }

        $log['step_5_distance_raw'] = $distances;

        // ========================
        // STEP 7 - SORTING
        // ========================
        usort($distances, fn($a, $b) => $a['distance'] <=> $b['distance']);
        $log['step_6_sorted'] = $distances;

        // ========================
        // STEP 8 - FINAL RESULT
        // ========================
        $final = array_slice($distances, 0, $k);
        $log['step_7_final_result'] = $final;

        return $log;
    }
}