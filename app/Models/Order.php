<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = ['batch_no'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'transactions')
            ->withPivot('quantity'); // Include the quantity of product sold in the pivot table
    }

        public function order_items()
        {
        return $this->hasMany(OrderItem::class);
        }
}
