<?php

/** @noinspection PhpUndefinedMethodInspection */

namespace App\Traits;

use App\Models\Discussion;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasDiscussions
{
    /**
     * Get all contacts for this company.
     */
    public function discussions(): MorphMany
    {
        return $this->morphMany(Discussion::class, 'discussable', 'on_type', 'on_id');
    }
}
