<?php

namespace App\Http\Controllers;

use App\Http\Requests\PatientRequest;
use App\Models\Patient;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class PatientController extends Controller
{
    /**
     * Display a listing of patients.
     */
    public function index()
    {
        $patients = Patient::with('created_by')
            ->search([
                'first_name',
                'last_name',
                'email'
            ])
            ->sort()
            ->paginate()
            ->withQueryString();

        return Inertia::render('Patients/Index', [
            'patients' => $patients,
        ]);
    }

    /**
     * Store a newly created patient in storage.
     */
    public function store(PatientRequest $request)
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);

        $patient = Patient::create($validated);

        return redirect()
            ->route('patients.show', $patient->id)
            ->with('message', 'Patient created successfully.')
            ->with('type', 'success');
    }

    /**
     * Show the form for creating a new patient.
     */
    public function create()
    {
        return Inertia::render('Patients/Form');
    }

    /**
     * Show the form for editing the specified patient.
     */
    public function edit(Patient $patient)
    {
        return Inertia::render('Patients/Form', [
            'patient' => $patient,
        ]);
    }

    /**
     * Display the specified patient.
     */
    public function show(Patient $patient)
    {
        $patient->load(['created_by']);
        return Inertia::render('Patients/Show', ['patient' => $patient]);
    }

    /**
     * Update the specified patient in storage.
     */
    public function update(PatientRequest $request, Patient $patient)
    {
        $validated = $request->validated();

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $patient->update($validated);

        return redirect()
            ->route('patients.show', $patient->id)
            ->with('message', 'Patient updated successfully.')
            ->with('type', 'success');
    }

    /**
     * Remove the specified patient from storage.
     */
    public function destroy(Patient $patient)
    {
        $patient->delete();

        return redirect()
            ->route('patients.index')
            ->with('message', 'Patient deleted successfully.')
            ->with('type', 'success');
    }
}
