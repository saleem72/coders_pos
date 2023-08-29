<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'unit_id',
        'name',
        'purchase',
        'retail',
        'quantity',
        'barcode',
        'image',
        'image_extension'
    ];

    /**
    * Get the category that owns the this category.
    */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
    * Get the category that owns the this category.
    */
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function invoices() {
        return $this->hasManyThrough(
            Invoice::class,
            InvoiceItem::class,
            'product_id', // Foreign key on the environments table...
            'id', // Foreign key on the deployments table...
            'id', // Local key on the projects table...
            'invoice_id'
        );
    }
}
