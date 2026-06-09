<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ListSearchService
{
    public static function apply(Builder $query, Request $request, array $columns, array $relations = []): Builder
    {
        if (! $request->filled('search') || (count($columns) === 0 && count($relations) === 0)) {
            return $query;
        }

        $search = trim((string) $request->query('search'));

        if ($search === '') {
            return $query;
        }

        return $query->where(function (Builder $query) use ($columns, $relations, $search): void {
            foreach ($columns as $column) {
                $query->orWhere($column, 'like', "%{$search}%");
            }

            foreach ($relations as $relation => $relationColumns) {
                $query->orWhereHas($relation, function (Builder $query) use ($relationColumns, $search): void {
                    foreach ($relationColumns as $column) {
                        $query->orWhere($column, 'like', "%{$search}%");
                    }
                });
            }
        });
    }
}
