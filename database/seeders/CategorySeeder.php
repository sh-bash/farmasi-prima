<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Master\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [

            // Analgesic & Fever
            'Analgesik & Antipiretik',

            // Antibiotik
            'Antibiotik',

            // Antiviral / Antifungal
            'Antivirus',
            'Antijamur',
            'Antiparasit',

            // Respiratory
            'Obat Flu & Batuk',
            'Antihistamin',
            'Dekongestan',
            'Asma & PPOK',

            // Gastro
            'Antasida & Maag',
            'Antidiare',
            'Laksatif',
            'Probiotik',
            'Anti Mual & Muntah',

            // Cardiovascular
            'Hipertensi',
            'Jantung',
            'Kolesterol',
            'Antikoagulan',

            // Diabetes
            'Diabetes',

            // Saraf
            'Neurologi',
            'Antiepilepsi',
            'Antidepresan',
            'Psikiatri',

            // Pain & Inflammation
            'Anti Inflamasi (NSAID)',
            'Kortikosteroid',

            // Kulit
            'Obat Kulit',
            'Jerawat',

            // Mata & THT
            'Obat Mata',
            'Obat Telinga',
            'Obat Hidung',
            'Obat Tenggorokan',

            // Vitamin & Supplement
            'Vitamin & Suplemen',
            'Imun Booster',
            'Mineral',
            'Herbal',

            // Reproduksi
            'Kontrasepsi',
            'Kesehatan Wanita',
            'Kesehatan Pria',

            // Anak
            'Obat Anak',

            // Lainnya
            'Anestesi',
            'Onkologi',
            'Imunologi',
            'Antialergi',
            'Emergency',
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['name' => $category]);
        }
    }
}