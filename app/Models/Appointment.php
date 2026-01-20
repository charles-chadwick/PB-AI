<?php

namespace App\Models;

use App\Enums\AppointmentStatus;
use App\Traits\ModelToSelect;
use App\Traits\Searchable;
use App\Traits\Sortable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Appointment extends Base
{
    use LogsActivity, Searchable, Sortable, ModelToSelect;

    protected $fillable = [
        'patient_id',
        'title',
        'type',
        'description',
        'appointment_date',
        'start_time',
        'end_time',
        'status',
    ];

    protected $appends = [
        'formatted_date',
        'formatted_time',
        'formatted_datetime',
    ];

    protected function casts(): array
    {
        return [
            'appointment_date' => 'date',
            'start_time' => 'datetime:H:i',
            'end_time' => 'datetime:H:i',
            'status' => AppointmentStatus::class,
        ];
    }

    /**
     * Get the patient that owns the appointment.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the users assigned to this appointment.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withTimestamps();
    }

    /**
     * Get the formatted date.
     */
    public function formattedDate(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->appointment_date?->format('M d, Y')
        );
    }

    /**
     * Get the formatted time range.
     */
    public function formattedTime(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->start_time?->format('g:i A') . ' - ' . $this->end_time?->format('g:i A')
        );
    }

    /**
     * Get the formatted date and time.
     */
    public function formattedDatetime(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->formatted_date . ' at ' . $this->formatted_time
        );
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['patient_id', 'title', 'appointment_date', 'start_time', 'end_time', 'status'])
            ->logOnlyDirty();
    }
}
