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

    public function messages()
    {
        return $this->hasMany(Message::class, 'purchase_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    protected $fillable = [
        'user_id',
        'item_id',
        'address',
        'payment_method',
        'seller_id',
        'completed_at',
        'is_completed_by_buyer'
    ];

}
