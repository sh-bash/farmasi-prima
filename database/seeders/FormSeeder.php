<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Master\Form;

class FormSeeder extends Seeder
{
    public function run(): void
    {
        $forms = [
            'Tablet',
            'Tablet Salut',
            'Tablet Kunyah',
            'Tablet Effervescent',
            'Kaplet',
            'Kapsul',
            'Sirup',
            'Sirup Kering',
            'Suspensi',
            'Drops',
            'Injeksi',
            'Infus',
            'Krim',
            'Salep',
            'Gel',
            'Lotion',
            'Suppositoria',
            'Ovula',
            'Inhaler',
            'Nebulizer Solution',
            'Patch',
            'Spray',
            'Obat Tetes Mata',
            'Obat Tetes Telinga',
            'Obat Tetes Hidung',
            'Mouthwash',
            'Lozenges / Tablet Hisap',
            'Powder / Serbuk',
            'Granul',
            'Emulsi',
        ];

        foreach ($forms as $form) {
            Form::firstOrCreate(['name' => $form]);
        }
    }
}