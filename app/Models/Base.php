<?php

namespace App\Models;

use App\Traits\LogModelActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

use function auth;

abstract class Base extends Model
{
    use LogModelActivity, SoftDeletes;

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (auth()->check()) {
                $model->created_by_id = auth()->id();
                $model->updated_by_id = auth()->id();
            }
        });

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

    }

    /**
     * Load default relationships.
     */
    protected static function booted(): void
    {
        parent::booted();

        if (static::class !== User::class) {
            static::retrieved(function ($model) {
                $model->loadMissing([
                    'created_by',
                    'updated_by',
                    'deleted_by',
                ]);
            });
        }
    }

    /**
     * Get the fillable attributes.
     */
    public function getFillable(): array
    {
        return array_merge(
            $this->fillable,
            [
                'created_at',
                'updated_at',
                'deleted_at',
            ]
        );
    }

    public function getCasts(): array
    {
        return array_merge($this->casts, [
            'created_at' => 'datetime:m/d/Y h:i A',
            'updated_at' => 'datetime:m/d/Y h:i A',
            'deleted_at' => 'datetime:m/d/Y h:i A',
        ]);
    }

    /**
     * Get the user who created this record.
     */
    public function created_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    /**
     * Get the user who last updated this record.
     */
    public function updated_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }

    /**
     * Get the user who deleted this record.
     */
    public function deleted_by(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by_id');
    }
}
