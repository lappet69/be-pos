<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;
    protected $table = 'stock';
    protected $fillable = ['product_id', 'stock'];

    public function stock_items()
    {
        return $this->belongsTo(Product::class, 'foreign_key');
    }
}
