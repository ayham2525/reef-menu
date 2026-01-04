<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'code',
        'employee_id',
        'broker_id',
        'is_free',
        'total_amount',
        'status',
        'placed_at',
        'notes',
        'agency_name'
    ];

    protected $casts = [
        'is_free'   => 'boolean',
        'placed_at' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::creating(function (Order $o) {
            if (empty($o->code)) {
                $o->code = 'ORD-' . Str::upper(Str::random(6));
            }
        });
    }

    // Relations
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    public function broker()
    {
        return $this->belongsTo(Broker::class);
    }
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Helpers
    public function scopeForToday($q)
    {
        return $q->whereDate('placed_at', now()->toDateString());
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
