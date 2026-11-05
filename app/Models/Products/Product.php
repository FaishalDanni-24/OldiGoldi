<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'category_id',
    'user_id', 
    'sku', 
    'product_name', 
    'brand', 
    'model', 
    'product_description',
    'condition_grade',
    'condition_notes',
    'price',
    'stock',
    'product_status'
])]
#[Table(key: 'product_id')]
class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;
}
