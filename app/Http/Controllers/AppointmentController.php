<?php

namespace App\Http\Controllers;

use App\Http\Requests\AppointmentRequest;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use Inertia\Inertia;

class AppointmentController extends Controller
{
    /**
     * Display a listing of appointments.
     */
    public function index()
    {
        $appointments = Appointment::with(['patient', 'users'])
            ->search(['title', 'description'])
            ->sort()
            ->paginate()
            ->withQueryString();

        return Inertia::render('Appointments/Index', [
            'appointments' => $appointments,
        ]);
    }

    /**
     * Show the form for creating a new appointment.
     */
    public function create()
    {
        $users = User::orderBy('first_name')
            ->get(['id', 'first_name', 'last_name', 'role'])
            ->makeHidden(['avatar_url', 'initials']);

        return Inertia::render('Appointments/Form', [
            'users' => $users,
        ]);
    }

    /**
     * Store a newly created appointment in storage.
     */
    public function store(AppointmentRequest $request)
    {
        $validated = $request->validated();
        $user_ids = $validated['user_ids'] ?? [];
        unset($validated['user_ids']);

        // Ensure patient_id is an integer
        if (isset($validated['patient_id'])) {
            $validated['patient_id'] = (int) $validated['patient_id'];
        }

        $appointment = Appointment::create($validated);

        if (!empty($user_ids)) {
            $appointment->users()->attach($user_ids);
        }

        return redirect()
            ->route('appointments.show', $appointment->id)
            ->with('message', 'Appointment created successfully.')
            ->with('type', 'success');
    }

    /**
     * Display the specified appointment.
     */
    public function show(Appointment $appointment)
    {
        $appointment->load(['patient', 'users', 'created_by']);

        return Inertia::render('Appointments/Show', [
            'appointment' => $appointment,
        ]);
    }

    /**
     * Show the form for editing the specified appointment.
     */
    public function edit(Appointment $appointment)
    {
        $appointment->load(['users', 'patient']);

        $users = User::orderBy('first_name')
            ->get(['id', 'first_name', 'middle_name', 'last_name', 'role'])
            ->makeHidden(['avatar_url', 'initials']);

        return Inertia::render('Appointments/Form', [
            'appointment' => $appointment,
            'users' => $users,
        ]);
    }

    /**
     * Update the specified appointment in storage.
     */
    public function update(AppointmentRequest $request, Appointment $appointment)
    {
        $validated = $request->validated();
        $user_ids = $validated['user_ids'] ?? [];
        unset($validated['user_ids']);

        // Ensure patient_id is an integer
        if (isset($validated['patient_id'])) {
            $validated['patient_id'] = (int) $validated['patient_id'];
        }

        $appointment->update($validated);

        if (!empty($user_ids)) {
            $appointment->users()->sync($user_ids);
        }

        return redirect()
            ->route('appointments.show', $appointment->id)
            ->with('message', 'Appointment updated successfully.')
            ->with('type', 'success');
    }

    /**
     * Remove the specified appointment from storage.
     */
    public function destroy(Appointment $appointment)
    {
        $appointment->delete();

        return redirect()
            ->route('appointments.index')
            ->with('message', 'Appointment deleted successfully.')
            ->with('type', 'success');
    }
}
