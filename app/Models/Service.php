<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'percentage',
    ];

    public function serviceLogs()
    {
        return $this->hasMany(ServiceLog::class);
    }

}