<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Sortable
{
    public function scopeSort(Builder $query): void
    {
        $sort_by = request('sort_by', 'created_at');
        $current_direction = request('sort_direction', 'desc');
        $direction = $current_direction === 'desc' ? 'asc' : 'desc';

        if (str_contains($sort_by, '.')) {
            [
                $relation,
                $field
            ] = explode('.', $sort_by, 2);

            $relation_instance = $query->getModel()
                ->$relation();
            $related_table = $relation_instance->getRelated()
                ->getTable();
            $foreign_key = $relation_instance->getForeignKeyName();
            $owner_key = $relation_instance->getOwnerKeyName();

            $query->join($related_table, "{$query->getModel()->getTable()}.{$foreign_key}", '=', "{$related_table}.{$owner_key}")
                ->orderBy("{$related_table}.{$field}", $direction)
                ->select("{$query->getModel()->getTable()}.*");
        } else {
            $query->orderBy($sort_by, $direction);
        }
    }
}
