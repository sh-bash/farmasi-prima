<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Master\Ingredient;

class IngredientSeeder extends Seeder
{
    public function run(): void
    {
        $ingredients = [
            'Phenylephrine',
            'Pseudoephedrine',
            'Dextromethorphan',
            'Guaifenesin',
            'Diphenhydramine',
            'Caffeine',
            'Attapulgite',
            'Povidone Iodine',
            'Herbal Extract',
        ];
        // $ingredients = [

        //     /*
        //     |--------------------------------------------------------------------------
        //     | Analgesic & Antipyretic
        //     |--------------------------------------------------------------------------
        //     */
        //     'Paracetamol',
        //     'Ibuprofen',
        //     'Aspirin',
        //     'Mefenamic Acid',
        //     'Diclofenac Sodium',
        //     'Diclofenac Potassium',
        //     'Ketorolac',
        //     'Meloxicam',
        //     'Piroxicam',
        //     'Naproxen',
        //     'Celecoxib',
        //     'Etoricoxib',

        //     /*
        //     |--------------------------------------------------------------------------
        //     | Antibiotics
        //     |--------------------------------------------------------------------------
        //     */
        //     'Amoxicillin',
        //     'Ampicillin',
        //     'Cefixime',
        //     'Cefadroxil',
        //     'Ceftriaxone',
        //     'Cefotaxime',
        //     'Ceftazidime',
        //     'Azithromycin',
        //     'Clarithromycin',
        //     'Erythromycin',
        //     'Levofloxacin',
        //     'Ciprofloxacin',
        //     'Metronidazole',
        //     'Clindamycin',
        //     'Doxycycline',
        //     'Tetracycline',
        //     'Co-Amoxiclav',

        //     /*
        //     |--------------------------------------------------------------------------
        //     | Antifungal
        //     |--------------------------------------------------------------------------
        //     */
        //     'Fluconazole',
        //     'Ketoconazole',
        //     'Itraconazole',
        //     'Nystatin',
        //     'Miconazole',
        //     'Clotrimazole',

        //     /*
        //     |--------------------------------------------------------------------------
        //     | Antiviral
        //     |--------------------------------------------------------------------------
        //     */
        //     'Acyclovir',
        //     'Oseltamivir',
        //     'Tenofovir',
        //     'Lamivudine',

        //     /*
        //     |--------------------------------------------------------------------------
        //     | Allergy & Cold
        //     |--------------------------------------------------------------------------
        //     */
        //     'Chlorpheniramine Maleate',
        //     'Loratadine',
        //     'Cetirizine',
        //     'Fexofenadine',
        //     'Diphenhydramine',
        //     'Phenylephrine',
        //     'Pseudoephedrine',
        //     'Dextromethorphan',
        //     'Guaifenesin',
        //     'Ambroxol',
        //     'Bromhexine',
        //     'Salbutamol',

        //     /*
        //     |--------------------------------------------------------------------------
        //     | Gastrointestinal
        //     |--------------------------------------------------------------------------
        //     */
        //     'Omeprazole',
        //     'Lansoprazole',
        //     'Pantoprazole',
        //     'Esomeprazole',
        //     'Ranitidine',
        //     'Famotidine',
        //     'Sucralfate',
        //     'Aluminium Hydroxide',
        //     'Magnesium Hydroxide',
        //     'Domperidone',
        //     'Metoclopramide',
        //     'Ondansetron',
        //     'Loperamide',
        //     'Oral Rehydration Salt',

        //     /*
        //     |--------------------------------------------------------------------------
        //     | Diabetes
        //     |--------------------------------------------------------------------------
        //     */
        //     'Metformin',
        //     'Glibenclamide',
        //     'Glimepiride',
        //     'Insulin Aspart',
        //     'Insulin Lispro',
        //     'Insulin Glargine',
        //     'Insulin Detemir',

        //     /*
        //     |--------------------------------------------------------------------------
        //     | Cardiovascular
        //     |--------------------------------------------------------------------------
        //     */
        //     'Amlodipine',
        //     'Nifedipine',
        //     'Captopril',
        //     'Enalapril',
        //     'Lisinopril',
        //     'Valsartan',
        //     'Losartan',
        //     'Bisoprolol',
        //     'Propranolol',
        //     'Furosemide',
        //     'Spironolactone',
        //     'Hydrochlorothiazide',
        //     'Simvastatin',
        //     'Atorvastatin',
        //     'Clopidogrel',
        //     'Warfarin',

        //     /*
        //     |--------------------------------------------------------------------------
        //     | Neurology & Psychiatry
        //     |--------------------------------------------------------------------------
        //     */
        //     'Diazepam',
        //     'Alprazolam',
        //     'Clonazepam',
        //     'Carbamazepine',
        //     'Valproic Acid',
        //     'Phenytoin',
        //     'Gabapentin',
        //     'Amitriptyline',
        //     'Fluoxetine',
        //     'Sertraline',
        //     'Haloperidol',
        //     'Risperidone',

        //     /*
        //     |--------------------------------------------------------------------------
        //     | Corticosteroid
        //     |--------------------------------------------------------------------------
        //     */
        //     'Dexamethasone',
        //     'Methylprednisolone',
        //     'Prednisone',
        //     'Prednisolone',
        //     'Hydrocortisone',
        //     'Betamethasone',
        //     'Triamcinolone',

        //     /*
        //     |--------------------------------------------------------------------------
        //     | Dermatology
        //     |--------------------------------------------------------------------------
        //     */
        //     'Gentamicin',
        //     'Mupirocin',
        //     'Fusidic Acid',
        //     'Silver Sulfadiazine',
        //     'Benzoyl Peroxide',
        //     'Adapalene',

        //     /*
        //     |--------------------------------------------------------------------------
        //     | Vitamins & Supplements
        //     |--------------------------------------------------------------------------
        //     */
        //     'Vitamin A',
        //     'Vitamin B1',
        //     'Vitamin B6',
        //     'Vitamin B12',
        //     'Vitamin C',
        //     'Vitamin D3',
        //     'Vitamin E',
        //     'Folic Acid',
        //     'Calcium Carbonate',
        //     'Calcium Lactate',
        //     'Zinc',
        //     'Iron',
        //     'Magnesium',
        //     'Multivitamin',

        //     /*
        //     |--------------------------------------------------------------------------
        //     | Emergency & Hospital Use
        //     |--------------------------------------------------------------------------
        //     */
        //     'Epinephrine',
        //     'Norepinephrine',
        //     'Dopamine',
        //     'Dobutamine',
        //     'Atropine',
        //     'Midazolam',
        //     'Propofol',
        //     'Ketamine',
        //     'Lidocaine',
        //     'Bupivacaine',

        //     /*
        //     |--------------------------------------------------------------------------
        //     | IV Fluids
        //     |--------------------------------------------------------------------------
        //     */
        //     'Sodium Chloride',
        //     'Ringer Lactate',
        //     'Dextrose',
        //     'Dextrose Saline',
        // ];

        foreach ($ingredients as $ingredient) {
            Ingredient::firstOrCreate(['name' => $ingredient]);
        }
    }
}