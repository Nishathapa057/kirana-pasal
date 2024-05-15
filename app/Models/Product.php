<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'image', 'barcode', 'description', 'qty','stock', 'price'];

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'transactions')
            ->withPivot('quantity'); // Include the quantity of product sold in the pivot table
    }
}
