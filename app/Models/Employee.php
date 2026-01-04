<?php
// app/Models/Employee.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'position_id',
        'section_id',
        'employee_code',
        'phone',
        'national_id',
        'gender',
        'birth_date',
        'hired_at',
        'terminated_at',
        'is_active',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'birth_date'  => 'date',
        'hired_at'    => 'date',
        'terminated_at' => 'date',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function position()
    {
        return $this->belongsTo(Position::class);
    }
    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    // Scopes
    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    protected static function booted()
    {
        static::creating(function (Employee $e) {
            if (empty($e->employee_code)) {
                // e.g., EMP-2F9KQW (unique-ish, short, human friendly)
                $e->employee_code = 'EMP-' . Str::upper(Str::random(6));
            }
        });
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
