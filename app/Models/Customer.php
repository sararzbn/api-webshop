<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'id', 'job_title', 'email', 'first_name_last_name', 'registered_since', 'phone',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
