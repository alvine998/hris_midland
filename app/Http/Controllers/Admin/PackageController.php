<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PackageController extends Controller
{
    public function index(Request $request): View
    {
        $packages = Package::query()
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%'.$request->string('search').'%')->orWhere('slug', 'like', '%'.$request->string('search').'%'))
            ->when($request->filled('plan') && $request->input('plan') !== 'all', fn ($q) => $q->where('plan', $request->input('plan')))
            ->when($request->filled('status') && $request->input('status') !== 'all', fn ($q) => $q->where('is_active', $request->boolean('status')))
            ->latest()->paginate(15)->withQueryString();

        return view('admin.packages.index', ['packages' => $packages, 'plans' => ['free', 'pro', 'enterprise', 'custom']]);
    }

    public function create(): View
    {
        return view('admin.packages.create', ['plans' => ['free', 'pro', 'enterprise', 'custom']]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = Str::slug($data['slug'] ?? $data['name']);
        Package::create($data);

        return redirect()->route('admin.packages.index')->with('success', 'Package created.');
    }

    public function edit(Package $package): View
    {
        return view('admin.packages.edit', ['package' => $package, 'plans' => ['free', 'pro', 'enterprise', 'custom']]);
    }

    public function update(Request $request, Package $package): RedirectResponse
    {
        $data = $this->validated($request, $package->id);
        $data['slug'] = Str::slug($data['slug'] ?? $data['name']);
        $package->update($data);

        return redirect()->route('admin.packages.index')->with('success', 'Package updated.');
    }

    public function destroy(Package $package): RedirectResponse
    {
        $package->delete();

        return redirect()->route('admin.packages.index')->with('success', 'Package deleted.');
    }

    public function toggleActive(Package $package): RedirectResponse
    {
        $package->update(['is_active' => ! $package->is_active]);

        return back()->with('success', 'Package '.($package->is_active ? 'activated' : 'deactivated').'.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        foreach (['price', 'discount_percent', 'max_employees', 'duration_days', 'sort_order'] as $k) {
            if ($request->has($k) && is_string($request->input($k))) {
                $request->merge([$k => str_replace('.', '', $request->input($k)) ?: null]);
            }
        }

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9-]+$/', 'unique:packages,slug'.($ignoreId ? ','.$ignoreId : '')],
            'plan' => ['required', 'in:free,pro,enterprise,custom'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'integer', 'min:0'],
            'discount_percent' => ['nullable', 'integer', 'min:0', 'max:100'],
            'sale_starts_at' => ['nullable', 'date'],
            'sale_ends_at' => ['nullable', 'date', 'after_or_equal:sale_starts_at'],
            'max_employees' => ['nullable', 'integer', 'min:1'],
            'duration_days' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
    }
}
