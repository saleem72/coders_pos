<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'parent_id'
    ];

    /**
    * Get the category that owns the this category.
    */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Get the category's sub categories.
     */
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * Get the category's sub categories.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function children_products() {
        return $this->hasManyThrough(
            Product::class,
            Category::class,
            'parent_id', // Foreign key on the environments table...
            'category_id', // Foreign key on the deployments table...
            'id', // Local key on the projects table...
            'id'
        );
    }
}
