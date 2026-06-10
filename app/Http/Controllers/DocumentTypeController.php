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
        $documentType = DocumentType::create($request->validated());
        $this->logCreated($documentType);

        return back()->with('success', 'Document type created successfully.');
    }

    public function update(StoreDocumentTypeRequest $request, DocumentType $documentType): RedirectResponse
    {
        $oldData = $documentType->attributesToArray();
        $documentType->update($request->validated());
        $this->logUpdated($documentType, $oldData);

        return back()->with('success', 'Document type updated successfully.');
    }

    public function destroy(DocumentType $documentType): RedirectResponse
    {
        $oldData = $documentType->attributesToArray();
        $documentType->delete();
        $this->logDeleted($documentType, $oldData);

        return back()->with('success', 'Document type deleted successfully.');
    }
}
