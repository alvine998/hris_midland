<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentRequest;
use App\Models\Document;
use Illuminate\Http\RedirectResponse;

class DocumentController extends Controller
{
    public function store(StoreDocumentRequest $request): RedirectResponse
    {
        Document::create($request->validated());

        return back()->with('success', 'Document created successfully.');
    }

    public function update(StoreDocumentRequest $request, Document $document): RedirectResponse
    {
        $document->update($request->validated());

        return back()->with('success', 'Document updated successfully.');
    }

    public function destroy(Document $document): RedirectResponse
    {
        $document->delete();

        return back()->with('success', 'Document deleted successfully.');
    }
}
