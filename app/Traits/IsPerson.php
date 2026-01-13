<?php

namespace App\Traits;

trait IsPerson
{
    public static function bootIsPerson(): void
    {
        static::retrieved(function ($model) {
            $model->appends = array_merge($model->appends, [
                'avatar',
                'full_name',
                'full_name_with_salutations',
            ]);
        });
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getFullNameWithSalutationsAttribute(): string
    {
        return trim($this?->prefix.' '.$this->first_name.' '.$this->last_name.' '.$this?->suffix);
    }

    public function getInitialsAttribute(): string
    {
        return $this->first_name[0].$this->last_name[0];
    }
}
