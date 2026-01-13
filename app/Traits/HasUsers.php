<?php

namespace App\Traits;

trait HasUsers
{
    public function loadRelations(): void
    {
        $this->with[] = 'created_by';
        $this->with[] = 'updated_by';
    }
}
