<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AiChatKnowledge extends Model
{
    protected $fillable = ['category', 'title', 'content', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeInCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    public function scopeMatching(Builder $query, string $term): Builder
    {
        if ($query->getConnection()->getDriverName() === 'sqlite') {
            return $query->where(function (Builder $q) use ($term) {
                foreach (preg_split('/\s+/', $term) as $w) {
                    $w = trim($w, "+* \t\n\r\0\x0B");
                    if ($w !== '') {
                        $q->orWhere('title', 'LIKE', "%{$w}%")->orWhere('content', 'LIKE', "%{$w}%");
                    }
                }
            });
        }

        return $query->whereFullText(['title', 'content'], $term, ['mode' => 'BOOLEAN']);
    }

    public function scopeRelevant(Builder $query, string $term, int $limit = 5): Builder
    {
        if ($query->getConnection()->getDriverName() === 'sqlite') {
            return $query->active()
                ->where(function (Builder $q) use ($term) {
                    foreach (preg_split('/\s+/', $term) as $w) {
                        $w = trim($w, "+* \t\n\r\0\x0B");
                        if ($w !== '') {
                            $q->orWhere('title', 'LIKE', "%{$w}%")->orWhere('content', 'LIKE', "%{$w}%");
                        }
                    }
                })
                ->limit($limit);
        }

        return $query->active()
            ->whereFullText(['title', 'content'], $term, ['mode' => 'BOOLEAN'])
            ->orderByRaw('MATCH(title, content) AGAINST(? IN BOOLEAN MODE) DESC', [$term])
            ->limit($limit);
    }
}
