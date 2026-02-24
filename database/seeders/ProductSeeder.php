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
        // ===== TRUNCATE =====
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('product_ingredients')->truncate();
        DB::table('products')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        /*
        Category
        1 Analgesic
        2 Antibiotic
        3 Flu & Batuk
        4 Gastro
        5 Cardiovascular
        6 Diabetes
        7 Vitamin
        8 Topical
        */

        /*
        Form
        1 Tablet
        2 Kapsul
        7 Syrup
        13 Solution/Topical
        */

        $products = [

        // ======================
        // ANALGESIC / PAIN
        // ======================
        ['Panadol', [['Paracetamol',500,'mg']],1,1,12000],
        ['Panadol Extra', [['Paracetamol',500,'mg'],['Caffeine',65,'mg']],1,1,15000],
        ['Sanmol', [['Paracetamol',500,'mg']],1,1,8000],
        ['Pamol', [['Paracetamol',500,'mg']],1,1,7000],
        ['Bodrex', [['Paracetamol',500,'mg'],['Caffeine',50,'mg']],1,1,7000],
        ['Oskadon', [['Paracetamol',500,'mg'],['Caffeine',50,'mg']],1,1,7000],
        ['Tempra', [['Paracetamol',500,'mg']],1,1,9000],
        ['Proris', [['Ibuprofen',200,'mg']],1,1,12000],
        ['Brufen', [['Ibuprofen',400,'mg']],1,1,15000],
        ['Ponstan', [['Mefenamic Acid',500,'mg']],1,1,11000],
        ['Mefinal', [['Mefenamic Acid',500,'mg']],1,1,10000],
        ['Voltaren', [['Diclofenac Sodium',50,'mg']],1,1,15000],
        ['Cataflam', [['Diclofenac Potassium',50,'mg']],1,1,18000],
        ['Ketorolac IF', [['Ketorolac',10,'mg']],1,1,14000],
        ['Meloxicam IF', [['Meloxicam',7.5,'mg']],1,1,13000],
        ['Celebrex', [['Celecoxib',200,'mg']],1,1,35000],

        // ======================
        // FLU & COLD (multi ingredient)
        // ======================
        ['Mixagrip Flu', [['Paracetamol',500,'mg'],['Phenylephrine',5,'mg'],['CTM',2,'mg']],3,1,8000],
        ['Mixagrip Flu Batuk', [['Paracetamol',500,'mg'],['Phenylephrine',5,'mg'],['CTM',2,'mg'],['Dextromethorphan',15,'mg']],3,1,9000],
        ['Neozep Forte', [['Paracetamol',500,'mg'],['Pseudoephedrine',30,'mg'],['CTM',2,'mg']],3,1,10000],
        ['Procold', [['Paracetamol',500,'mg'],['Phenylephrine',5,'mg'],['CTM',2,'mg']],3,1,9000],
        ['Decolgen', [['Paracetamol',500,'mg'],['Phenylephrine',5,'mg'],['CTM',2,'mg']],3,1,9000],
        ['Bodrex Flu', [['Paracetamol',500,'mg'],['Phenylephrine',5,'mg'],['Dextromethorphan',15,'mg']],3,1,8500],
        ['Ultraflu', [['Paracetamol',500,'mg'],['Pseudoephedrine',30,'mg'],['CTM',2,'mg']],3,1,9000],
        ['Actifed', [['Pseudoephedrine',30,'mg'],['Triprolidine',2.5,'mg']],3,1,20000],

        // ======================
        // COUGH
        // ======================
        ['OBH Combi', [['Dextromethorphan',15,'mg'],['Guaifenesin',100,'mg'],['CTM',2,'mg']],3,7,12000],
        ['Komix', [['Dextromethorphan',15,'mg'],['Diphenhydramine',12.5,'mg']],3,7,5000],
        ['Siladex', [['Dextromethorphan',15,'mg']],3,7,13000],
        ['Vicks Formula 44', [['Dextromethorphan',15,'mg']],3,7,20000],

        // ======================
        // GASTRO
        // ======================
        ['Promag', [['Aluminium Hydroxide',200,'mg'],['Magnesium Hydroxide',200,'mg'],['Simethicone',50,'mg']],4,1,7000],
        ['Mylanta', [['Aluminium Hydroxide',200,'mg'],['Magnesium Hydroxide',200,'mg']],4,7,15000],
        ['Polysilane', [['Simethicone',80,'mg']],4,1,9000],
        ['Entrostop', [['Attapulgite',650,'mg']],4,1,6000],
        ['Diapet', [['Herbal Extract',1,'unit']],4,1,7000],
        ['Omeprazole IF', [['Omeprazole',20,'mg']],4,2,15000],
        ['Lansoprazole IF', [['Lansoprazole',30,'mg']],4,2,18000],
        ['Pantoprazole IF', [['Pantoprazole',40,'mg']],4,2,20000],
        ['Domperidone IF', [['Domperidone',10,'mg']],4,1,12000],
        ['Ondansetron IF', [['Ondansetron',4,'mg']],4,1,25000],

        // ======================
        // ANTIBIOTIC
        // ======================
        ['Amoxicillin', [['Amoxicillin',500,'mg']],2,1,12000],
        ['Amoxsan', [['Amoxicillin',500,'mg']],2,1,15000],
        ['Hufanoxil', [['Amoxicillin',500,'mg']],2,1,14000],
        ['Clamoxyl', [['Amoxicillin',500,'mg']],2,1,20000],
        ['Co-Amoxiclav', [['Amoxicillin',500,'mg'],['Clavulanic Acid',125,'mg']],2,1,45000],
        ['Augmentin', [['Amoxicillin',500,'mg'],['Clavulanic Acid',125,'mg']],2,1,65000],
        ['Cefadroxil', [['Cefadroxil',500,'mg']],2,1,22000],
        ['Cefixime', [['Cefixime',200,'mg']],2,1,35000],
        ['Fixam', [['Cefixime',200,'mg']],2,1,40000],
        ['Ceftriaxone', [['Ceftriaxone',1,'g']],2,13,25000],
        ['Azithromycin', [['Azithromycin',500,'mg']],2,1,45000],
        ['Zithromax', [['Azithromycin',500,'mg']],2,1,55000],
        ['Ciprofloxacin', [['Ciprofloxacin',500,'mg']],2,1,18000],
        ['Levofloxacin', [['Levofloxacin',500,'mg']],2,1,30000],
        ['Metronidazole', [['Metronidazole',500,'mg']],2,1,10000],

        // ======================
        // CARDIO
        // ======================
        ['Amlodipine', [['Amlodipine',5,'mg']],5,1,8000],
        ['Norvasc', [['Amlodipine',5,'mg']],5,1,30000],
        ['Captopril', [['Captopril',25,'mg']],5,1,6000],
        ['Enalapril', [['Enalapril',10,'mg']],5,1,9000],
        ['Losartan', [['Losartan',50,'mg']],5,1,15000],
        ['Valsartan', [['Valsartan',80,'mg']],5,1,25000],
        ['Bisoprolol', [['Bisoprolol',5,'mg']],5,1,12000],
        ['Propranolol', [['Propranolol',10,'mg']],5,1,8000],
        ['Simvastatin', [['Simvastatin',20,'mg']],5,1,9000],
        ['Atorvastatin', [['Atorvastatin',20,'mg']],5,1,20000],

        // ======================
        // DIABETES
        // ======================
        ['Metformin', [['Metformin',500,'mg']],6,1,7000],
        ['Glucophage', [['Metformin',500,'mg']],6,1,20000],
        ['Glimepiride', [['Glimepiride',2,'mg']],6,1,15000],
        ['Amaryl', [['Glimepiride',2,'mg']],6,1,35000],
        ['Glibenclamide', [['Glibenclamide',5,'mg']],6,1,8000],

        // ======================
        // VITAMIN
        // ======================
        ['Redoxon', [['Vitamin C',1000,'mg']],7,1,20000],
        ['Enervon C', [['Vitamin C',500,'mg'],['Vitamin B Complex',1,'tab']],7,1,12000],
        ['Imboost', [['Echinacea',250,'mg'],['Zinc',10,'mg']],7,1,30000],
        ['Blackmores C', [['Vitamin C',1000,'mg']],7,1,50000],
        ['Vitamin B Complex', [['Vitamin B1',10,'mg'],['Vitamin B6',5,'mg'],['Vitamin B12',5,'mcg']],7,1,8000],

        // ======================
        // TOPICAL
        // ======================
        ['Betadine Solution', [['Povidone Iodine',10,'%']],8,13,20000],
        ['Betadine Gargle', [['Povidone Iodine',1,'%']],8,13,18000],
        ['Bioplacenton', [['Neomycin',5,'mg']],8,13,25000],
        ['Burnazin', [['Silver Sulfadiazine',1,'%']],8,13,30000],
        ['Fucidin', [['Fusidic Acid',2,'%']],8,13,35000],
        ];

        // ===== Insert =====
        $barcode = 880000000000;

        foreach ($products as $p) {

            $product = Product::create([
                'barcode' => $barcode++,
                'name' => $p[0],
                'category_id' => $p[2],
                'form_id' => $p[3],
                'het' => $p[4],
            ]);

            foreach ($p[1] as $ing) {

                $ingredient = Ingredient::firstOrCreate([
                    'name' => $ing[0]
                ]);

                DB::table('product_ingredients')->insert([
                    'product_id' => $product->id,
                    'ingredient_id' => $ingredient->id,
                    'strength' => $ing[1],
                    'unit' => $ing[2],
                ]);
            }
        }
    }
}