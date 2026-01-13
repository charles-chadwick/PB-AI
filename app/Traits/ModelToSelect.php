<?php

namespace App\Traits;

trait ModelToSelect
{
    public function toSelect(): array
    {
        return $this->get()
            ->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->name,
                ];
            })
            ->toArray();
    }
}
