<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'price',
        'percentage',
        'is_variable_price',
        'uses_quantity'
    ];

    public function serviceLogs()
    {
        return $this->hasMany(ServiceLog::class);
    }

}