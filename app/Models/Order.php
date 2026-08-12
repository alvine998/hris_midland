<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = ['order_code', 'package_id', 'company_id', 'company_name', 'email', 'phone', 'plan', 'price', 'paid_amount', 'status', 'notes', 'admin_notes', 'payment_method', 'bank_account_id', 'voucher_code', 'payment_proof'];

    protected function casts(): array
    {
        return ['price' => 'integer', 'paid_amount' => 'integer'];
    }

    protected static function booted(): void
    {
        static::creating(function (Order $o) {
            if (empty($o->order_code)) {
                $o->order_code = 'ORD-'.strtoupper(Str::random(8)).'-'.now()->format('ymd');
                while (static::where('order_code', $o->order_code)->exists()) {
                    $o->order_code = 'ORD-'.strtoupper(Str::random(8)).'-'.now()->format('ymd');
                }
            }
        });
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function scopePending($q)
    {
        return $q->where('status', 'pending');
    }
}
