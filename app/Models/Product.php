<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $fillable = [
        'name',
        'brand_id',
        'detail',
        'price',
    ];

    public function brand() {
        return $this->belongsTo(Brand::class);
    }
    protected $casts = [
        'price' => 'decimal:2',
    ];
}
