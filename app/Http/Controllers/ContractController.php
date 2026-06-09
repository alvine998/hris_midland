<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContractRequest;
use App\Models\Contract;
use Illuminate\Http\RedirectResponse;

class ContractController extends Controller
{
    public function store(StoreContractRequest $request): RedirectResponse
    {
        Contract::create($request->validated());

        return redirect()->back()->with('success', 'Contract created successfully.');
    }

    public function update(StoreContractRequest $request, Contract $contract): RedirectResponse
    {
        $contract->update($request->validated());

        return redirect()->back()->with('success', 'Contract updated successfully.');
    }

    public function destroy(Contract $contract): RedirectResponse
    {
        $contract->delete();

        return redirect()->back()->with('success', 'Contract deleted successfully.');
    }
}
