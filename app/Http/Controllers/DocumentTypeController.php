<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentTypeRequest;
use App\Models\DocumentType;
use App\Services\ListSearchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DocumentTypeController extends Controller
{
    public function index(Request $request): View
    {
        $documentTypes = ListSearchService::apply(DocumentType::query(), $request, ['name'])
            ->paginate(10)
            ->withQueryString();

        return view('master-data.document-types', ['documentTypes' => $documentTypes]);
    }

    public function store(StoreDocumentTypeRequest $request): RedirectResponse
    {
        DocumentType::create($request->validated());

        return back()->with('success', 'Document type created successfully.');
    }

    public function update(StoreDocumentTypeRequest $request, DocumentType $documentType): RedirectResponse
    {
        $documentType->update($request->validated());

        return back()->with('success', 'Document type updated successfully.');
    }

    public function destroy(DocumentType $documentType): RedirectResponse
    {
        $documentType->delete();

        return back()->with('success', 'Document type deleted successfully.');
    }
}
