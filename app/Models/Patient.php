<?php

namespace App\Models;

use App\Traits\IsPerson;
use App\Traits\Searchable;
use App\Traits\Sortable;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;

class Patient extends Base implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract, HasMedia
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use Authenticatable, Authorizable, CanResetPassword, HasFactory, Notifiable, HasRoles, LogsActivity, InteractsWithMedia;
    use IsPerson, Searchable, Sortable;

    protected $appends = [
        'avatar_url',
        'full_name',
        'initials',
    ];

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'date_of_birth',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts() : array
    {
        return [
            'date_of_birth' => 'date:Y-m-d',
            'password' => 'hashed',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['date_of_birth', 'first_name', 'middle_name', 'last_name', 'email'])
            ->logOnlyDirty();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')->singleFile();
    }
}
