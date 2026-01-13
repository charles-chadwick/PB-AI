<?php

namespace App\Traits;

use function activity;
use function array_intersect_key;
use function auth;

trait LogModelActivity
{
    public static function bootLogModelActivity(): void
    {
        static::updating(function ($model) {
            if (auth()->check()) {
                $model->updated_by_id = auth()->id();
            }
        });

        static::deleting(function ($model) {
            if (auth()->check()) {
                $model->deleted_by_id = auth()->id();
                $model->save();
            }
        });

        static::creating(function ($model) {
            if (auth()->check()) {
                $model->created_by_id = auth()->id();
                $model->updated_by_id = auth()->id();
            }
        });

        // Activity logging
        static::created(function ($model) {
            if (auth()->check()) {
                activity()
                    ->performedOn($model)
                    ->causedBy(auth()->user())
                    ->useLog('Database')
                    ->log('Created');
            }
        });

        static::updated(function ($model) {
            if (auth()->check()) {
                $changes = $model->getChanges();
                $original = $model->getOriginal();
                $properties = [
                    'attributes' => $changes,
                    'old' => array_intersect_key($original, $changes),
                ];

                activity()
                    ->performedOn($model)
                    ->causedBy(auth()->user())
                    ->withProperties($properties)
                    ->useLog('Database')
                    ->log('Updated');
            }
        });

        static::deleted(function ($model) {
            if (auth()->check()) {
                activity()
                    ->performedOn($model)
                    ->causedBy(auth()->user())
                    ->useLog('Database')
                    ->log('Deleted');
            }
        });

        static::restored(function ($model) {
            if (auth()->check()) {
                activity()
                    ->performedOn($model)
                    ->causedBy(auth()->user())
                    ->useLog('Database')
                    ->log('Restored');
            }
        });

        static::forceDeleted(function ($model) {
            if (auth()->check()) {
                activity()
                    ->performedOn($model)
                    ->causedBy(auth()->user())
                    ->useLog('Database')
                    ->log('Permanently deleted');
            }
        });
    }
}
