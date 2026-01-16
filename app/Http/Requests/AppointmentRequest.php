<?php

namespace App\Http\Requests;

use App\Models\Appointment;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class AppointmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'patient_id' => 'required|exists:patients,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'appointment_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'status' => 'required|in:scheduled,completed,cancelled,no_show',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'required|exists:users,id',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->failed()) {
                return;
            }

            $this->checkPatientDoubleBooking($validator);
            $this->checkUsersDoubleBooking($validator);
        });
    }

    /**
     * Check if the patient is double booked.
     */
    private function checkPatientDoubleBooking(Validator $validator): void
    {
        $patient_id = $this->input('patient_id');
        $appointment_date = $this->input('appointment_date');
        $start_time = $this->input('start_time');
        $end_time = $this->input('end_time');
        $appointment_id = $this->route('appointment')?->id;

        $overlapping = Appointment::where('patient_id', $patient_id)
            ->where('appointment_date', $appointment_date)
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($start_time, $end_time) {
                $query->whereBetween('start_time', [$start_time, $end_time])
                    ->orWhereBetween('end_time', [$start_time, $end_time])
                    ->orWhere(function ($q) use ($start_time, $end_time) {
                        $q->where('start_time', '<=', $start_time)
                          ->where('end_time', '>=', $end_time);
                    });
            });

        if ($appointment_id) {
            $overlapping->where('id', '!=', $appointment_id);
        }

        if ($overlapping->exists()) {
            $validator->errors()->add(
                'appointment_date',
                'This patient already has an appointment during this time.'
            );
        }
    }

    /**
     * Check if any of the users are double booked.
     */
    private function checkUsersDoubleBooking(Validator $validator): void
    {
        $user_ids = $this->input('user_ids', []);
        $appointment_date = $this->input('appointment_date');
        $start_time = $this->input('start_time');
        $end_time = $this->input('end_time');
        $appointment_id = $this->route('appointment')?->id;

        $overlapping = Appointment::where('appointment_date', $appointment_date)
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($start_time, $end_time) {
                $query->whereBetween('start_time', [$start_time, $end_time])
                    ->orWhereBetween('end_time', [$start_time, $end_time])
                    ->orWhere(function ($q) use ($start_time, $end_time) {
                        $q->where('start_time', '<=', $start_time)
                          ->where('end_time', '>=', $end_time);
                    });
            })
            ->whereHas('users', function ($query) use ($user_ids) {
                $query->whereIn('users.id', $user_ids);
            });

        if ($appointment_id) {
            $overlapping->where('id', '!=', $appointment_id);
        }

        if ($overlapping->exists()) {
            $overlapping_appointment = $overlapping->with('users')->first();
            $overlapping_users = $overlapping_appointment->users
                ->filter(fn($user) => in_array($user->id, $user_ids))
                ->pluck('full_name')
                ->join(', ');

            $validator->errors()->add(
                'user_ids',
                "The following users are already booked during this time: {$overlapping_users}"
            );
        }
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'user_ids.required' => 'At least one staff member must be assigned to the appointment.',
            'user_ids.*.exists' => 'One or more selected staff members do not exist.',
        ];
    }
}
