<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    protected $fillable = [
        'user_id',        // 購入者ID
        'item_id',        // 商品ID  
        'address',        // 配送先住所
        'payment_method'  // 支払い方法
    ];

}
