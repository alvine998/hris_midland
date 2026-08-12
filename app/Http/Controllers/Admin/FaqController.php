<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function index(Request $request): View
    {
        $faqs = Faq::query()
            ->when($request->filled('search'), fn ($q) => $q->where('question', 'like', '%'.$request->string('search').'%')->orWhere('answer', 'like', '%'.$request->string('search').'%'))
            ->when($request->filled('category') && $request->input('category') !== 'all', fn ($q) => $q->where('category', $request->input('category')))
            ->when($request->filled('status') && $request->input('status') !== 'all', fn ($q) => $q->where('is_active', $request->boolean('status')))
            ->ordered()->paginate(15)->withQueryString();
        $categories = Faq::whereNotNull('category')->distinct()->pluck('category')->sort()->values();

        return view('admin.faqs.index', compact('faqs', 'categories'));
    }

    public function create(): View
    {
        return view('admin.faqs.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Faq::create($this->validated($request));

        return redirect()->route('admin.faqs.index')->with('success', 'FAQ created.');
    }

    public function edit(Faq $faq): View
    {
        return view('admin.faqs.edit', compact('faq'));
    }

    public function update(Request $request, Faq $faq): RedirectResponse
    {
        $faq->update($this->validated($request));

        return redirect()->route('admin.faqs.index')->with('success', 'FAQ updated.');
    }

    public function destroy(Faq $faq): RedirectResponse
    {
        $faq->delete();

        return redirect()->route('admin.faqs.index')->with('success', 'FAQ deleted.');
    }

    public function toggleActive(Faq $faq): RedirectResponse
    {
        $faq->update(['is_active' => ! $faq->is_active]);

        return back()->with('success', 'FAQ '.($faq->is_active ? 'activated' : 'deactivated').'.');
    }

    private function validated(Request $request): array
    {
        if ($request->has('sort_order') && is_string($request->input('sort_order'))) {
            $request->merge(['sort_order' => str_replace('.', '', $request->input('sort_order')) ?: null]);
        }

        return $request->validate([
            'question' => ['required', 'string', 'max:500'],
            'answer' => ['required', 'string'],
            'category' => ['nullable', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
    }
}
