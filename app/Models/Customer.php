<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'address',
        'image',
        'image_extension'
    ];

    public function invoices() {
        return $this->hasMany(Invoice::class);
    }
}
