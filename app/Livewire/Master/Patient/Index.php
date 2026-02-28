<?php

namespace App\Livewire\Master\Patient;

use App\Models\Master\Patient;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $medical_record_number, $name, $gender, $phone, $address, $birth_date;
    public $patientId;
    public $search = '';
    public $isEdit = false;
    public $showForm = false;
    public $showTable = true;
    public $deleteId;

    protected function rules()
    {
        return [
            'medical_record_number' => [
                'required',
                Rule::unique('patients', 'medical_record_number')
                    ->whereNull('deleted_at')
                    ->ignore($this->patientId)
            ],
            'name' => 'required|string',
            'gender' => 'required',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'birth_date' => 'nullable|date'
        ];
    }

    public function render()
    {
        $patients = Patient::where('name', 'like', '%'.$this->search.'%')
            ->orWhere('medical_record_number', 'like', '%'.$this->search.'%')
            ->latest()
            ->paginate(5);

        return view('livewire.master.patient.index', compact('patients'))
            ->layout('layouts.app', [
                'title' => 'Master Patient',
                'subtitle' => 'Manage patient data',
            ]);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function toggleForm()
    {
        $this->showForm = !$this->showForm;
    }

    public function toggleTable()
    {
        $this->showTable = !$this->showTable;
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();

        DB::beginTransaction();

        try {

            // ================================
            // EDIT MODE
            // ================================
            if ($this->patientId) {

                $patient = Patient::findOrFail($this->patientId);

                $patient->update([
                    'medical_record_number' => $this->medical_record_number,
                    'name' => $this->name,
                    'gender' => $this->gender,
                    'phone' => $this->phone,
                    'address' => $this->address,
                    'birth_date' => $this->birth_date,
                ]);

                // Optional: update name di user juga
                if ($patient->user) {
                    $patient->user->update([
                        'name' => $this->name,
                    ]);
                }

            }
            // ================================
            // CREATE MODE
            // ================================
            else {

                // generate email otomatis kalau tidak ada
                $email = $this->phone . '@primahusada.com';

                // cek kalau sudah ada user dg email tsb
                $existingUser = User::where('email', $email)->first();

                if ($existingUser) {
                    throw new \Exception('User dengan nomor ini sudah ada.');
                }

                // 1️⃣ create user
                $user = User::create([
                    'name' => $this->name,
                    'email' => $email,
                    'password' => Hash::make('12345678'),
                ]);

                $user->assignRole('patient');

                // 2️⃣ create patient
                Patient::create([
                    'medical_record_number' => $this->medical_record_number,
                    'name' => $this->name,
                    'gender' => $this->gender,
                    'phone' => $this->phone,
                    'address' => $this->address,
                    'birth_date' => $this->birth_date,
                    'user_id' => $user->id,
                ]);
            }

            DB::commit();

            $this->resetForm();
            $this->showForm = false;

            $this->dispatch('swal',
                icon: 'success',
                title: 'Success',
                text: 'Patient saved successfully'
            );

        } catch (\Exception $e) {

            DB::rollBack();

            $this->dispatch('swal',
                icon: 'error',
                title: 'Error',
                text: $e->getMessage()
            );
        }
    }

    public function edit($id)
    {
        $patient = Patient::findOrFail($id);

        $this->patientId = $patient->id;
        $this->medical_record_number = $patient->medical_record_number;
        $this->name = $patient->name;
        $this->gender = $patient->gender;
        $this->phone = $patient->phone;
        $this->address = $patient->address;
        $this->birth_date = $patient->birth_date;

        $this->isEdit = true;
        $this->showForm = true;
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;

        $this->dispatch('swal-confirm',
            title: 'Delete Patient?',
            text: 'This data will be moved to trash'
        );
    }

    public function delete()
    {
        Patient::find($this->deleteId)?->delete();

        $this->dispatch('swal',
            icon: 'success',
            title: 'Deleted',
            text: 'Patient deleted'
        );
    }

    public function resetForm()
    {
        $this->medical_record_number = '';
        $this->name = '';
        $this->gender = '';
        $this->phone = '';
        $this->address = '';
        $this->birth_date = '';
        $this->patientId = null;
        $this->isEdit = false;
    }
}