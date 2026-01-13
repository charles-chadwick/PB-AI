<?php

namespace App\Traits;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasContacts
{
    /**
     * Get all contacts for this company.
     */
    public function contacts(): MorphMany
    {
        return $this->morphMany(Contact::class, 'contactable', 'on_type', 'on_id');
    }
}
