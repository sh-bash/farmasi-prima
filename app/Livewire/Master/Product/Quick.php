<?php

namespace App\Livewire\Master\Product;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\Master\Product;

class Quick extends Component
{
    public $barcode;
    public $name;
    public $het;
    public $category_id;
    public $form_id;
    public $is_generic = 0;

    public $ingredients = [];

    protected $listeners = [
        'setCategory',
        'setForm',
        'setIngredient',
        'addIngredientRow',
        'removeIngredientRow'
    ];

    public function setCategory($value)
    {
        $this->category_id = $value;
    }

    public function setForm($value)
    {
        $this->form_id = $value;
    }

    public function setIngredient($index, $value)
    {
        $this->ingredients[$index]['ingredient_id'] = $value;
    }

    public function mount()
    {
        $this->addIngredientRow();
    }

    /* ==============================
       INGREDIENT ROW
    ============================== */

    public function addIngredientRow()
    {
        $this->ingredients[] = [
            'ingredient_id' => null,
            'strength' => null,
            'unit' => 'mg',
        ];

        $this->dispatch('reinit-ingredients');
    }

    public function removeIngredientRow($index)
    {
        unset($this->ingredients[$index]);
        $this->ingredients = array_values($this->ingredients);
    }

    /* ==============================
       VALIDATION
    ============================== */

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'barcode' => 'nullable|string|max:100',
            'het' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'form_id' => 'required|exists:forms,id',

            'ingredients.*.ingredient_id' => 'required|exists:ingredients,id',
            'ingredients.*.strength' => 'required|numeric|min:0',
            'ingredients.*.unit' => 'required|string|max:20',
        ];
    }

    /* ==============================
       SAVE
    ============================== */

    public function save()
    {
        // $this->validate();

        DB::transaction(function () {

            $product = Product::create([
                'barcode' => $this->barcode,
                'name' => $this->name,
                'het' => $this->het,
                'category_id' => $this->category_id,
                'form_id' => $this->form_id,
                // 'is_generic' => $this->is_generic,
                'created_by' => auth()->id(),
            ]);


            foreach ($this->ingredients as $ingredient) {
                DB::table('product_ingredients')->insert([
                    'product_id'    => $product->id,
                    'ingredient_id' => $ingredient['ingredient_id'],
                    'strength'      => $ingredient['strength'],
                    'unit'          => $ingredient['unit'],
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }

            // $this->dispatch('productCreated', [
            //     'id' => $product->id,
            //     'name' => $product->name,
            //     'price' => $product->het
            // ]);
        });

        $this->resetForm();

        $this->dispatch('closeProductModal');
        $this->dispatch('swal',
            icon: 'success',
            title: 'Success',
            text: 'Product created successfully'
        );
    }

    private function resetForm()
    {
        $this->reset([
            'barcode',
            'name',
            'het',
            'category_id',
            'form_id',
            'is_generic',
            'ingredients'
        ]);

        $this->ingredients = [];
        $this->addIngredientRow();
    }

    /* ==============================
       RENDER
    ============================== */

    public function render()
    {
        $categories = DB::table('categories')->get();
        $forms = DB::table('forms')->get();
        $ingredientList = DB::table('ingredients')->get();

        return view('livewire.master.product.quick', compact(
            'categories',
            'forms',
            'ingredientList'
        ));
    }
}