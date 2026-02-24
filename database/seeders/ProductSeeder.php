<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Master\Product;
use App\Models\Master\Ingredient;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('products')->truncate();
        DB::table('product_ingredients')->truncate();

        // ===== MASTER LIST =====

        $products = [

            // ======================
            // ANALGESIC (Category 1)
            // Form: Tablet = 1
            // ======================
            ['Paracetamol 500 Tablet', 'Paracetamol', 500, 'mg', 1, 1, 5000],
            ['Sanmol Tablet', 'Paracetamol', 500, 'mg', 1, 1, 7000],
            ['Pamol Tablet', 'Paracetamol', 500, 'mg', 1, 1, 6500],
            ['Bodrex Tablet', 'Paracetamol', 500, 'mg', 1, 1, 7500],

            // Syrup (Form 7)
            ['Sanmol Syrup', 'Paracetamol', 120, 'mg/5ml', 1, 7, 12000],
            ['Tempra Syrup', 'Paracetamol', 120, 'mg/5ml', 1, 7, 15000],

            // NSAID
            ['Ibuprofen 200 Tablet', 'Ibuprofen', 200, 'mg', 1, 1, 8000],
            ['Ibuprofen 400 Tablet', 'Ibuprofen', 400, 'mg', 1, 1, 12000],
            ['Asam Mefenamat 500', 'Mefenamic Acid', 500, 'mg', 1, 1, 9000],
            ['Diclofenac Sodium 50', 'Diclofenac Sodium', 50, 'mg', 1, 1, 8500],

            // ======================
            // ANTIBIOTIC (Category 2)
            // ======================
            ['Amoxicillin 500', 'Amoxicillin', 500, 'mg', 2, 1, 12000],
            ['Amoxicillin 250', 'Amoxicillin', 250, 'mg', 2, 1, 9000],
            ['Amoxicillin Syrup', 'Amoxicillin', 125, 'mg/5ml', 2, 7, 18000],

            ['Cefixime 200', 'Cefixime', 200, 'mg', 2, 1, 35000],
            ['Cefadroxil 500', 'Cefadroxil', 500, 'mg', 2, 1, 25000],
            ['Azithromycin 500', 'Azithromycin', 500, 'mg', 2, 1, 45000],

            // ======================
            // COLD & ALLERGY (Category 3)
            // ======================
            ['CTM Tablet', 'Chlorpheniramine Maleate', 4, 'mg', 3, 1, 4000],
            ['Loratadine 10', 'Loratadine', 10, 'mg', 3, 1, 9000],
            ['Cetirizine 10', 'Cetirizine', 10, 'mg', 3, 1, 8500],

            // Combination
            [
                'Decolgen Tablet',
                [
                    ['Paracetamol', 500],
                    ['Phenylephrine', 5],
                    ['Chlorpheniramine Maleate', 2]
                ],
                null,
                null,
                3,
                1,
                9000
            ],

            // ======================
            // GASTRO (Category 4)
            // ======================
            ['Omeprazole 20', 'Omeprazole', 20, 'mg', 4, 2, 15000],
            ['Lansoprazole 30', 'Lansoprazole', 30, 'mg', 4, 2, 18000],
            ['Antasida DOEN', 'Aluminium Hydroxide', 200, 'mg', 4, 1, 5000],

            // ======================
            // CARDIO (Category 5)
            // ======================
            ['Amlodipine 5', 'Amlodipine', 5, 'mg', 5, 1, 8000],
            ['Amlodipine 10', 'Amlodipine', 10, 'mg', 5, 1, 10000],
            ['Captopril 25', 'Captopril', 25, 'mg', 5, 1, 6000],

            // ======================
            // DIABETES (Category 6)
            // ======================
            ['Metformin 500', 'Metformin', 500, 'mg', 6, 1, 7000],
            ['Metformin 850', 'Metformin', 850, 'mg', 6, 1, 9000],
            ['Glimepiride 2', 'Glimepiride', 2, 'mg', 6, 1, 15000],

            // ======================
            // VITAMIN (Category 7)
            // ======================
            ['Vitamin C 500', 'Vitamin C', 500, 'mg', 7, 1, 5000],
            ['Vitamin D3 1000', 'Vitamin D3', 1000, 'IU', 7, 1, 12000],
            ['Zinc Tablet', 'Zinc', 20, 'mg', 7, 1, 8000],
            [
                'Panadol Regular',
                'Paracetamol', 500, 'mg',
                1, 1, 12000
            ],
            [
                'Panadol Extra',
                [
                    ['Paracetamol', 500],
                    ['Caffeine', 65]
                ],
                null, null,
                1, 1, 15000
            ],
            [
                'Bodrex',
                [
                    ['Paracetamol', 500],
                    ['Caffeine', 50]
                ],
                null, null,
                1, 1, 8000
            ],
            [
                'Oskadon',
                [
                    ['Paracetamol', 500],
                    ['Caffeine', 50]
                ],
                null, null,
                1, 1, 7000
            ],
            [
                'Mixagrip Flu',
                [
                    ['Paracetamol', 500],
                    ['Phenylephrine', 5],
                    ['Chlorpheniramine Maleate', 2]
                ],
                null, null,
                3, 1, 8000
            ],
            [
                'Mixagrip Flu & Batuk',
                [
                    ['Paracetamol', 500],
                    ['Dextromethorphan', 15],
                    ['Phenylephrine', 5],
                    ['Chlorpheniramine Maleate', 2]
                ],
                null, null,
                3, 1, 9000
            ],
            [
                'Neozep Forte',
                [
                    ['Paracetamol', 500],
                    ['Pseudoephedrine', 30],
                    ['Chlorpheniramine Maleate', 2]
                ],
                null, null,
                3, 1, 10000
            ],
            [
                'Procold Flu',
                [
                    ['Paracetamol', 500],
                    ['Phenylephrine', 5],
                    ['Chlorpheniramine Maleate', 2]
                ],
                null, null,
                3, 1, 9000
            ],
            [
                'Bodrex Flu & Batuk',
                [
                    ['Paracetamol', 500],
                    ['Dextromethorphan', 15],
                    ['Phenylephrine', 5]
                ],
                null, null,
                3, 1, 8500
            ],
            [
                'Mixagrip Flu',
                [
                    ['Paracetamol', 500],
                    ['Phenylephrine', 5],
                    ['Chlorpheniramine Maleate', 2]
                ],
                null, null,
                3, 1, 8000
            ],
            [
                'Mixagrip Flu & Batuk',
                [
                    ['Paracetamol', 500],
                    ['Dextromethorphan', 15],
                    ['Phenylephrine', 5],
                    ['Chlorpheniramine Maleate', 2]
                ],
                null, null,
                3, 1, 9000
            ],
            [
                'Neozep Forte',
                [
                    ['Paracetamol', 500],
                    ['Pseudoephedrine', 30],
                    ['Chlorpheniramine Maleate', 2]
                ],
                null, null,
                3, 1, 10000
            ],
            [
                'Procold Flu',
                [
                    ['Paracetamol', 500],
                    ['Phenylephrine', 5],
                    ['Chlorpheniramine Maleate', 2]
                ],
                null, null,
                3, 1, 9000
            ],
            [
                'Bodrex Flu & Batuk',
                [
                    ['Paracetamol', 500],
                    ['Dextromethorphan', 15],
                    ['Phenylephrine', 5]
                ],
                null, null,
                3, 1, 8500
            ],
            [
                'Promag',
                [
                    ['Aluminium Hydroxide', 200],
                    ['Magnesium Hydroxide', 200]
                ],
                null, null,
                4, 1, 7000
            ],
            [
                'Mylanta',
                [
                    ['Aluminium Hydroxide', 200],
                    ['Magnesium Hydroxide', 200]
                ],
                null, null,
                4, 7, 15000
            ],
            [
                'Entrostop',
                [
                    ['Attapulgite', 650]
                ],
                null, null,
                4, 1, 6000
            ],
            [
                'Diapet',
                'Herbal Extract', 1, 'unit',
                4, 1, 7000
            ],
            [
                'Betadine Solution',
                'Povidone Iodine', 10, '%',
                8, 13, 20000
            ],
            [
                'Betadine Gargle',
                'Povidone Iodine', 1, '%',
                8, 30, 18000
            ],
        ];

        // ===== DUPLICATE TO REACH 200 =====

        $targetCount = 200;
        $i = 1;

        while ($i <= $targetCount) {

            $base = $products[array_rand($products)];

            $product = Product::create([
                'barcode' => 'PRD' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'name' => $base[0] . ' ' . rand(1, 5), // variasi brand
                'category_id' => $base[4],
                'form_id' => $base[5],
                'het' => $base[6],
            ]);

            // ===== INGREDIENT =====

            if (is_array($base[1])) {
                foreach ($base[1] as $ing) {
                    $ingredient = Ingredient::firstOrCreate([
                        'name' => $ing[0]
                    ]);

                    DB::table('product_ingredients')->insert([
                        'product_id' => $product->id,
                        'ingredient_id' => $ingredient->id,
                        'strength' => $ing[1],
                        'unit' => 'mg'
                    ]);
                }
            } else {
                $ingredient = Ingredient::firstOrCreate([
                    'name' => $base[1]
                ]);

                DB::table('product_ingredients')->insert([
                    'product_id' => $product->id,
                    'ingredient_id' => $ingredient->id,
                    'strength' => $base[2],
                    'unit' => $base[3]
                ]);
            }

            $i++;
        }
    }
}