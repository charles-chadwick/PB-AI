<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;

trait IsPerson
{
    public function avatarUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                $path = $this->getFirstMediaPath('avatar');
                if ($path && file_exists($path)) {
                    $url = $this->getFirstMediaUrl('avatar');
                    return str_replace(config('app.url'), config('app.url').':'.config('app.port'), $url);
                }
                return null;
            }
        );
    }

    public function fullName(): Attribute
    {
        return Attribute::make(
            get: function () {
                return collect([
                    $this->first_name,
                    $this->middle_name,
                    $this->last_name,
                ])->filter()->implode(' ');
            }
        );
    }

    public function initials(): Attribute
    {
        return Attribute::make(
            get: function () {
                $first = $this->first_name ?? '';
                $last = $this->last_name ?? '';
                return strtoupper(substr($first, 0, 1) . substr($last, 0, 1));
            }
        );
    }
}
