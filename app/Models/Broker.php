<?php
// app/Models/Broker.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Broker extends Model
{
    use HasUuids;

    protected $fillable = ['agency_id', 'name', 'email', 'phone', 'brn', 'is_active'];

    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
