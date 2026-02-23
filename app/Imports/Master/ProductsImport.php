<?php
namespace App\Imports\Master;

use App\Models\Master\Product;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        DB::beginTransaction();

        try {

            foreach ($rows as $index => $row) {

                // --- VALIDASI KOSONG ---
                if (
                    empty($row['barcode']) ||
                    empty($row['name']) ||
                    empty($row['het'])
                ) {
                    throw new \Exception(
                        "Row ".($index + 2)." has empty data"
                    );
                }

                // --- CARI PRODUCT TERMASUK YANG SOFT DELETE ---
                $product = Product::withTrashed()
                    ->where('barcode', $row['barcode'])
                    ->first();

                if ($product) {

                    // Jika di trash → restore
                    if ($product->trashed()) {
                        $product->restore();
                    }

                    // Update data
                    $product->update([
                        'name' => $row['name'],
                        'het'  => $row['het'],
                        'updated_at' => Carbon::now()
                    ]);

                } else {

                    // Insert baru
                    Product::create([
                        'barcode' => $row['barcode'],
                        'name'    => $row['name'],
                        'het'     => $row['het']
                    ]);
                }
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}