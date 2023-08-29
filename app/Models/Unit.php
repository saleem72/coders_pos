<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    /**
     * Get the category's sub categories.
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'unit_id');
    }
}
