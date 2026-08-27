<?php

namespace App\Services;

use App\Models\AiChatKnowledge;
use Illuminate\Support\Collection;

class KnowledgeBaseService
{
    /**
     * Search the knowledge base for articles relevant to the query.
     * Falls back to LIKE search if FULLTEXT doesn't match.
     */
    public function search(string $query, int $limit = 5): Collection
    {
        $results = AiChatKnowledge::relevant($query, $limit)->get();

        if ($results->isEmpty()) {
            $results = $this->fallbackSearch($query, $limit);
        }

        return $results;
    }

    /**
     * Build a context string from matching articles to inject into the AI prompt.
     */
    public function buildContext(string $query, int $limit = 5): string
    {
        $articles = $this->search($query, $limit);

        if ($articles->isEmpty()) {
            return '';
        }

        $chunks = $articles->map(fn (AiChatKnowledge $a) => "[{$a->category}] {$a->title}\n{$a->content}");

        return "Relevant HR knowledge base articles:\n\n".$chunks->implode("\n\n");
    }

    /**
     * Extract keywords from a user query for FULLTEXT BOOLEAN mode.
     */
    public function toSearchTerm(string $query): string
    {
        $words = preg_split('/\s+/', trim($query));
        $stopWords = ['the', 'a', 'an', 'is', 'are', 'was', 'were', 'be', 'been', 'being',
            'have', 'has', 'had', 'do', 'does', 'did', 'will', 'would', 'could', 'should',
            'may', 'might', 'can', 'shall', 'to', 'of', 'in', 'for', 'on', 'with', 'at',
            'by', 'from', 'as', 'into', 'about', 'between', 'through', 'during', 'before',
            'after', 'above', 'below', 'and', 'but', 'or', 'nor', 'not', 'so', 'if', 'then',
            'than', 'too', 'very', 'just', 'that', 'this', 'these', 'those', 'i', 'me', 'my',
            'we', 'our', 'you', 'your', 'he', 'him', 'his', 'she', 'her', 'it', 'they', 'them',
            'their', 'what', 'which', 'who', 'whom', 'how', 'when', 'where', 'why'];

        $keywords = collect($words)
            ->map(fn (string $w) => preg_replace('/[^\w]/', '', strtolower($w)))
            ->filter(fn (string $w) => strlen($w) > 1 && ! in_array($w, $stopWords))
            ->values();

        if ($keywords->isEmpty()) {
            return $query;
        }

        return $keywords->map(fn (string $w) => "+{$w}*")->implode(' ');
    }

    protected function fallbackSearch(string $query, int $limit): Collection
    {
        $keywords = collect(explode(' ', $query))
            ->filter(fn (string $w) => strlen(trim($w)) > 1)
            ->take(5);

        if ($keywords->isEmpty()) {
            return collect();
        }

        return AiChatKnowledge::active()
            ->where(function ($q) use ($keywords) {
                foreach ($keywords as $word) {
                    $q->orWhere('title', 'LIKE', "%{$word}%")
                        ->orWhere('content', 'LIKE', "%{$word}%");
                }
            })
            ->limit($limit)
            ->get();
    }
}
