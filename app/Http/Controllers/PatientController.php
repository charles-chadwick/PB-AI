<?php

namespace App\Http\Controllers;

use App\Http\Requests\PatientRequest;
use App\Models\Patient;
use Illuminate\Http\Request;
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
     * Search for patients by name, date of birth, or ID.
     */
    public function search(Request $request)
    {
        $query = $request->input('q', '');

        if (empty($query)) {
            return response()->json([]);
        }

        $patients = Patient::query()
            ->where(function ($q) use ($query) {
                // Search by ID
                if (is_numeric($query)) {
                    $q->where('id', $query);
                }

                // Search by name (first, middle, last)
                $q->orWhere('first_name', 'LIKE', "%{$query}%")
                  ->orWhere('middle_name', 'LIKE', "%{$query}%")
                  ->orWhere('last_name', 'LIKE', "%{$query}%")
                  ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$query}%"])
                  ->orWhereRaw("CONCAT(first_name, ' ', middle_name, ' ', last_name) LIKE ?", ["%{$query}%"]);

                // Search by date of birth (YYYY-MM-DD format)
                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $query)) {
                    $q->orWhere('date_of_birth', $query);
                }
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->limit(10)
            ->get(['id', 'first_name', 'middle_name', 'last_name', 'date_of_birth'])
            ->makeHidden(['avatar_url', 'initials']);

        return response()->json($patients);
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

        $appointments = $patient->appointments()
            ->with(['users' => function ($query) {
                $query->select('users.id', 'first_name', 'last_name', 'role')
                    ->without(['created_by', 'updated_by', 'deleted_by']);
            }])
            ->orderBy('appointment_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->limit(5)
            ->get();

        $total_appointments = $patient->appointments()->count();

        $users = \App\Models\User::orderBy('first_name')
            ->get(['id', 'first_name', 'last_name', 'role'])
            ->makeHidden(['avatar_url', 'initials', 'full_name']);

        return Inertia::render('Patients/Show', [
            'patient' => $patient,
            'appointments' => $appointments,
            'total_appointments' => $total_appointments,
            'users' => $users,
        ]);
    }

    /**
     * Load more appointments for a patient (AJAX endpoint).
     */
    public function loadMoreAppointments(Request $request, Patient $patient)
    {
        $request->validate([
            'offset' => 'required|integer|min:0',
        ]);

        $appointments = $patient->appointments()
            ->with(['users' => function ($query) {
                $query->select('users.id', 'first_name', 'last_name', 'role')
                    ->without(['created_by', 'updated_by', 'deleted_by']);
            }])
            ->orderBy('appointment_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->offset($request->input('offset'))
            ->limit(5)
            ->get();

        return response()->json($appointments);
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
