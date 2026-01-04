<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean',
    ];

    public function stockMovement(Request $request)
    {
        $warehouses = Warehouse::orderBy('name')->get();
        $items = MenuItem::orderBy('name')->get();
        $users = User::orderBy('name')->get();   // ← ADD THIS

        $query = InventoryMovement::with(['creator', 'item', 'warehouse'])
            ->latest();

        // Date Range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Warehouse
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        // Item
        if ($request->filled('menu_item_id')) {
            $query->where('menu_item_id', $request->menu_item_id);
        }

        // Type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // User
        if ($request->filled('user_id')) {
            $query->where('created_by', $request->user_id);
        }

        $movements = $query->paginate(40);

        return view('admin.reports.stock-movement', compact(
            'movements',
            'warehouses',
            'items',
            'users'   // ← SEND TO VIEW
        ));
    }
}
