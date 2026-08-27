<?php

namespace App\Http\Controllers;

use App\Models\AiChatKnowledge;
use App\Services\ListSearchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AiChatKnowledgeController extends Controller
{
    public function index(Request $request): View
    {
        $articles = ListSearchService::apply(AiChatKnowledge::query(), $request, ['title', 'category'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $categories = AiChatKnowledge::select('category')->distinct()->orderBy('category')->pluck('category');

        return view('ai-chat-knowledge.index', ['articles' => $articles, 'categories' => $categories]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'category' => ['required', 'string', 'max:100'],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
        ]);

        AiChatKnowledge::create($data);
        $this->logCreated(new AiChatKnowledge, 'AI Chat Knowledge');

        return back()->with('success', 'Knowledge base article created.');
    }

    public function update(Request $request, AiChatKnowledge $article): RedirectResponse
    {
        $oldData = $article->attributesToArray();

        $data = $request->validate([
            'category' => ['required', 'string', 'max:100'],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $article->update($data);
        $this->logUpdated($article, $oldData, 'AI Chat Knowledge');

        return back()->with('success', 'Knowledge base article updated.');
    }

    public function destroy(AiChatKnowledge $article): RedirectResponse
    {
        $oldData = $article->attributesToArray();
        $article->delete();
        $this->logDeleted($article, $oldData, 'AI Chat Knowledge');

        return back()->with('success', 'Knowledge base article deleted.');
    }
}
