<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Master\Patient;
use Illuminate\Http\Request;

class PatientApiController extends Controller
{
    public function saleFind(Request $request)
    {
        $search = $request->q;

        if ($request->id) {

            $patient = Patient::find($request->id);

            if (!$patient) {
                return response()->json(['results' => []]);
            }

            return response()->json([
                'results' => [[
                    'id' => $patient->id,
                    'text' => $patient->name . ' (' . $patient->medical_record_number . ')',
                ]]
            ]);
        }

        $patients = Patient::query()
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('medical_record_number', 'like', "%{$search}%");
            })
            ->limit(20)
            ->get();

        return response()->json([
            'results' => $patients->map(function ($patient) {
                return [
                    'id' => $patient->id,
                    'text' => $patient->name . ' (' . $patient->medical_record_number . ')',
                ];
            })
        ]);
    }
}
