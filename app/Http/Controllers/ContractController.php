<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContractRequest;
use App\Models\Contract;
use Illuminate\Http\RedirectResponse;

class ContractController extends Controller
{
    public function store(StoreContractRequest $request): RedirectResponse
    {
        $contract = Contract::create($request->validated());
        $this->logCreated($contract);

        return redirect()->back()->with('success', 'Contract created successfully.');
    }

    public function update(StoreContractRequest $request, Contract $contract): RedirectResponse
    {
        $oldData = $contract->attributesToArray();
        $contract->update($request->validated());
        $this->logUpdated($contract, $oldData);

        return redirect()->back()->with('success', 'Contract updated successfully.');
    }

    public function destroy(Contract $contract): RedirectResponse
    {
        $oldData = $contract->attributesToArray();
        $contract->delete();
        $this->logDeleted($contract, $oldData);

        return redirect()->back()->with('success', 'Contract deleted successfully.');
    }
}
