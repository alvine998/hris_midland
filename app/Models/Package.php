<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Package extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'slug', 'plan', 'description', 'price', 'discount_percent', 'sale_price', 'sale_starts_at', 'sale_ends_at', 'max_employees', 'duration_days', 'is_active'];

    protected function casts(): array
    {
        return ['price' => 'integer', 'sale_price' => 'integer', 'is_active' => 'boolean', 'sale_starts_at' => 'datetime', 'sale_ends_at' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::saving(function (Package $p) {
            if (empty($p->slug) && ! empty($p->name)) {
                $p->slug = Str::slug($p->name);
            }
            $suffix = '';
            $base = $p->slug;
            $i = 2;
            while (static::where('slug', $p->slug)->where('id', '!=', $p->id ?? 0)->exists()) {
                $p->slug = $base.'-'.$i++;
            }
            // ponytail: sale_price auto-derived from discount_percent; upgrade to coupon engine if needed
            if ($p->discount_percent !== null && $p->discount_percent > 0) {
                $p->sale_price = (int) round($p->price * (100 - $p->discount_percent) / 100);
            } elseif ($p->discount_percent === null || $p->discount_percent === 0) {
                $p->sale_price = null;
            }
        });
    }

    public function getEffectivePriceAttribute(): int
    {
        if ($this->isOnSale()) {
            return (int) $this->sale_price;
        }

        return (int) $this->price;
    }

    public function isOnSale(): bool
    {
        if ($this->sale_price === null || $this->sale_price >= $this->price) {
            return false;
        }
        $now = now();
        if ($this->sale_starts_at && $now->lt($this->sale_starts_at)) {
            return false;
        }
        if ($this->sale_ends_at && $now->gt($this->sale_ends_at)) {
            return false;
        }

        return true;
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }
}
