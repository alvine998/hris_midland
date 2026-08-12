<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Ticket extends Model
{
    protected $fillable = ['ticket_code', 'name', 'email', 'subject', 'message', 'status', 'priority', 'company_id'];

    protected static function booted(): void
    {
        static::creating(function (Ticket $t) {
            if (empty($t->ticket_code)) {
                $t->ticket_code = 'TCK-'.strtoupper(Str::random(7)).'-'.now()->format('ymd');
                while (static::where('ticket_code', $t->ticket_code)->exists()) {
                    $t->ticket_code = 'TCK-'.strtoupper(Str::random(7)).'-'.now()->format('ymd');
                }
            }
        });
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function replies()
    {
        return $this->hasMany(TicketReply::class)->latest();
    }
}
