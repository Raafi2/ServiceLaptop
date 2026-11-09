<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_key',
        'phone',
    ];

    public function services(): HasMany
    {
        return $this->hasMany(Service::class)->orderByDesc('received_at');
    }
}
