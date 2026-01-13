<?php

namespace App\Traits;

trait Searchable
{
    public function scopeSearch($query, string|array $fields)
    {
        $search = request('search');
        if ($search == '') {
            return $query;
        }
        if (! is_array($fields)) {
            $fields = [$fields];
        }

        // cycle over the fields and pull out any with a dot in them. Sort them in another array
        $relation_fields = collect($fields)->filter(fn ($field) => str_contains($field, '.'))->values()->all();
        $fields = collect($fields)->filter(fn ($field) => ! str_contains($field, '.'))->values()->all();

        // query on regular fields
        return $query->when($search, fn ($query) => $query->whereAny($fields, 'LIKE', '%'.$search.'%'))
            // and then relation search on the relation fields
            ->when($search, function ($query) use ($search, $relation_fields) {
                foreach ($relation_fields as $field) {
                    [$relation, $column] = explode('.', $field);
                    $query->orWhereHas($relation, fn ($query) => $query->where($column, 'LIKE', '%'.$search.'%'));
                }
            });
    }
}
