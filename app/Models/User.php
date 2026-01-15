<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\IsPerson;
use App\Traits\Searchable;
use App\Traits\Sortable;
use Database\Factories\UserFactory;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;

class User extends Base implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract, HasMedia
{
    /** @use HasFactory<UserFactory> */
    use Authenticatable, Authorizable, CanResetPassword, HasFactory, Notifiable, HasRoles, LogsActivity, InteractsWithMedia;
    use IsPerson, Searchable, Sortable;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $appends = [
        'avatar_url',
        'full_name',
        'initials',
    ];

    protected $fillable = [
        'role',
        'first_name',
        'last_name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['role', 'first_name', 'last_name', 'email'])
            ->logOnlyDirty();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')->singleFile();
    }

    /**
     * Get the appointments assigned to this user.
     */
    public function appointments(): BelongsToMany
    {
        return $this->belongsToMany(Appointment::class)
            ->withTimestamps();
    }
}
