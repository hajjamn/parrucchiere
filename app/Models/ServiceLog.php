<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'client_id',
        'service_id',
        'custom_price',
        'commission_percentage',
        'custom_commission',
        'quantity',
        'is_part_of_subscription',
        'notes',
        'performed_at',

    ];

    protected $casts = [
        'performed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function client()
    {
        return $this->belongsTo(Client::class)->withTrashed();
    }

    public function service()
    {
        return $this->belongsTo(Service::class)->withTrashed();
    }
}
