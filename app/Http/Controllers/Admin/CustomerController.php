<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $customers = Company::query()
            ->withCount('employees')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('plan') && $request->input('plan') !== 'all', function ($query) use ($request) {
                $query->where('plan', $request->input('plan'));
            })
            ->when($request->filled('status') && $request->input('status') !== 'all', function ($query) use ($request) {
                $query->where('is_active', $request->boolean('status'));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.customers.index', [
            'customers' => $customers,
            'plans' => ['free', 'pro', 'enterprise'],
        ]);
    }

    public function show(Company $company): View
    {
        $company->loadCount('employees');

        return view('admin.customers.show', [
            'company' => $company,
        ]);
    }

    public function edit(Company $company): View
    {
        return view('admin.customers.edit', [
            'company' => $company,
            'plans' => ['free', 'pro', 'enterprise'],
        ]);
    }

    public function update(Request $request, Company $company): RedirectResponse
    {
        if ($request->has('max_employees') && is_string($request->input('max_employees'))) {
            $request->merge(['max_employees' => str_replace('.', '', $request->input('max_employees')) ?: null]);
        }
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'phone' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'plan' => ['required', 'in:free,pro,enterprise'],
            'max_employees' => ['nullable', 'integer', 'min:1'],
            'subscription_expires_at' => ['nullable', 'date'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $company->update($data);

        return redirect()->route('admin.customers.show', $company)
            ->with('success', 'Customer updated successfully.');
    }

    public function toggleActive(Company $company): RedirectResponse
    {
        $company->update([
            'is_active' => ! $company->is_active,
        ]);

        $status = $company->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Customer {$status} successfully.");
    }

    public function destroy(Company $company): RedirectResponse
    {
        $company->delete();

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer deleted successfully.');
    }
}
