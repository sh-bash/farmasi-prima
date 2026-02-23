<?php

namespace App\Imports\Master;

use App\Models\Master\Supplier;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SuppliersImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        DB::beginTransaction();

        try {

            foreach ($rows as $index => $row) {

                // VALIDASI KOSONG
                if (
                    empty($row['code']) ||
                    empty($row['name'])
                ) {
                    throw new \Exception(
                        "Row ".($index + 2)." has empty data"
                    );
                }

                // Cari termasuk yang soft delete
                $supplier = Supplier::withTrashed()
                    ->where('code', $row['code'])
                    ->first();

                if ($supplier) {

                    // Restore jika di trash
                    if ($supplier->trashed()) {
                        $supplier->restore();
                    }

                    // Update
                    $supplier->update([
                        'name' => $row['name'],
                        'location' => $row['location'] ?? null,
                        'contact' => $row['contact'] ?? null,
                        'person_in_charge' => $row['person_in_charge'] ?? null,
                    ]);

                } else {

                    // Insert baru
                    Supplier::create([
                        'code' => $row['code'],
                        'name' => $row['name'],
                        'location' => $row['location'] ?? null,
                        'contact' => $row['contact'] ?? null,
                        'person_in_charge' => $row['person_in_charge'] ?? null,
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