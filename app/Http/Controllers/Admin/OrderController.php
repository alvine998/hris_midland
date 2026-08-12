<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\CompanyCredentials;
use App\Models\Company;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = Order::with('package')
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = $request->string('search');
                $q->where(fn ($qq) => $qq->where('order_code', 'like', "%{$s}%")->orWhere('company_name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"));
            })
            ->when($request->filled('status') && $request->input('status') !== 'all', fn ($q) => $q->where('status', $request->input('status')))
            ->latest()->paginate(15)->withQueryString();

        return view('admin.orders.index', ['orders' => $orders]);
    }

    public function show(Order $order): View
    {
        $order->load(['package', 'company']);

        return view('admin.orders.show', ['order' => $order, 'companies' => Company::orderBy('name')->get(['id', 'name'])]);
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,approved,rejected,expired'],
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $order->update($data);

        if ($data['status'] === 'approved') {
            // ponytail: auto-provision Company per email; upgrade to multi-company / user invite when needed
            $company = null;

            if ($request->filled('company_id')) {
                $request->validate(['company_id' => ['required', 'exists:companies,id']]);
                $company = Company::find($request->input('company_id'));
                if ($company) {
                    $order->update(['company_id' => $company->id]);
                }
            } elseif ($order->company_id) {
                $company = $order->company ?? Company::withTrashed()->find($order->company_id);
            }

            if (! $company) {
                $company = Company::withTrashed()->where('email', $order->email)->first();
                if ($company?->trashed()) {
                    $company->restore();
                }
                if (! $company) {
                    $order->loadMissing('package');
                    $company = Company::create([
                        'name' => $order->company_name,
                        'email' => $order->email,
                        'phone' => $order->phone ?: '-',
                        'address' => '-',
                        'status' => 'active',
                        'plan' => $order->plan,
                        'max_employees' => $order->package?->max_employees,
                        'subscription_expires_at' => $order->package?->duration_days ? now()->addDays($order->package->duration_days) : null,
                        'is_active' => true,
                    ]);
                }
                $order->update(['company_id' => $company->id]);
            }

            if ($company) {
                $order->loadMissing('package');
                $company->update([
                    'plan' => $order->plan,
                    'max_employees' => $order->package?->max_employees ?? $company->max_employees,
                    'subscription_expires_at' => $order->package?->duration_days ? now()->addDays($order->package->duration_days) : $company->subscription_expires_at,
                    'is_active' => true,
                ]);

                $plainPassword = Str::password(12);
                $company->update(['password' => $plainPassword]);
                Mail::to($company->email)->queue(new CompanyCredentials($company, $plainPassword));
            }
        }

        return back()->with('success', 'Order status updated.');
    }

    public function destroy(Order $order): RedirectResponse
    {
        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', 'Order deleted.');
    }
}
