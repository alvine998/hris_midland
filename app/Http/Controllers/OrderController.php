<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Order;
use App\Models\Package;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function create(Package $package): View
    {
        abort_unless($package->is_active, 404);

        return view('orders.create', ['package' => $package, 'bankAccounts' => BankAccount::active()->orderBy('bank_name')->get()]);
    }

    public function store(Request $request, Package $package): RedirectResponse
    {
        abort_unless($package->is_active, 404);

        foreach (['phone'] as $k) {
            if ($request->has($k) && is_string($request->input($k)) && $request->input($k) === '') {
                $request->merge([$k => null]);
            }
        }

        $data = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'voucher_code' => ['nullable', 'string', 'max:50'],
            'payment_method' => ['required', 'in:bank_transfer'],
            'bank_account_id' => ['required_if:payment_method,bank_transfer', 'nullable', 'exists:bank_accounts,id'],
        ]);

        // Ensure free packages don't require payment selection to block flow — but validation already passed
        if ($package->effective_price == 0) {
            $data['payment_method'] = 'bank_transfer';
            $data['bank_account_id'] = null;
        } elseif (! empty($data['bank_account_id'])) {
            abort_unless(BankAccount::where('id', $data['bank_account_id'])->where('is_active', true)->exists(), 422, 'Selected bank account is inactive.');
        }

        $order = Order::create([
            'package_id' => $package->id,
            'company_name' => $data['company_name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'plan' => $package->plan,
            'price' => $package->effective_price,
            'status' => 'pending',
            'notes' => $data['notes'] ?? null,
            'voucher_code' => $data['voucher_code'] ?? null,
            'payment_method' => $data['payment_method'],
            'bank_account_id' => $data['bank_account_id'] ?? null,
        ]);

        return redirect()->route('orders.success', $order->order_code)->with('success', 'Order placed. We will contact you shortly.');
    }

    public function success(string $orderCode): View
    {
        $order = Order::with(['package', 'bankAccount'])->where('order_code', $orderCode)->firstOrFail();

        return view('orders.success', ['order' => $order]);
    }

    public function showUploadProof(string $orderCode): View
    {
        $order = Order::with(['package', 'bankAccount'])->where('order_code', $orderCode)->firstOrFail();
        abort_unless(in_array($order->status, ['pending']), 404);

        return view('orders.upload-proof', ['order' => $order]);
    }

    public function uploadProof(Request $request, string $orderCode): RedirectResponse
    {
        $order = Order::with('package')->where('order_code', $orderCode)->firstOrFail();
        abort_unless(in_array($order->status, ['pending']), 404);

        $request->validate([
            'payment_proof' => ['required', 'image', 'max:5120'],
        ]);

        if ($order->payment_proof) {
            Storage::disk('public')->delete($order->payment_proof);
        }

        $path = $request->file('payment_proof')->store('payment-proofs', 'public');
        $order->update(['payment_proof' => $path]);

        return redirect()->route('orders.success', $order->order_code)->with('success', 'Payment proof uploaded. We will review it shortly.');
    }
}
