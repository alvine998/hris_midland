<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRelationshipRequest;
use App\Models\Relationship;
use App\Services\ListSearchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RelationshipController extends Controller
{
    public function index(Request $request): View
    {
        $relationships = ListSearchService::apply(Relationship::query(), $request, ['name', 'description'])
            ->paginate(10)
            ->withQueryString();

        return view('master-data.relationships', ['relationships' => $relationships]);
    }

    public function store(StoreRelationshipRequest $request): RedirectResponse
    {
        Relationship::create($request->validated());

        return back()->with('success', 'Relationship created successfully.');
    }

    public function update(StoreRelationshipRequest $request, Relationship $relationship): RedirectResponse
    {
        $relationship->update($request->validated());

        return back()->with('success', 'Relationship updated successfully.');
    }

    public function destroy(Relationship $relationship): RedirectResponse
    {
        $relationship->delete();

        return back()->with('success', 'Relationship deleted successfully.');
    }
}
