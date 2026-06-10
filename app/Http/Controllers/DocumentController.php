<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentRequest;
use App\Models\Document;
use Illuminate\Http\RedirectResponse;

class DocumentController extends Controller
{
    public function store(StoreDocumentRequest $request): RedirectResponse
    {
        $document = Document::create($request->validated());
        $this->logCreated($document);

        return back()->with('success', 'Document created successfully.');
    }

    public function update(StoreDocumentRequest $request, Document $document): RedirectResponse
    {
        $oldData = $document->attributesToArray();
        $document->update($request->validated());
        $this->logUpdated($document, $oldData);

        return back()->with('success', 'Document updated successfully.');
    }

    public function destroy(Document $document): RedirectResponse
    {
        $oldData = $document->attributesToArray();
        $document->delete();
        $this->logDeleted($document, $oldData);

        return back()->with('success', 'Document deleted successfully.');
    }
}
