<?php
// app/Models/Agency.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Agency extends Model
{
    use HasUuids;

    protected $fillable = ['name', 'code', 'email', 'phone', 'license_no', 'is_active'];

    public function brokers()
    {
        return $this->hasMany(Broker::class);
    }
}
