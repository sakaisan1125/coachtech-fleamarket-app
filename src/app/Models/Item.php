<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'image_path',
        'condition',
        'price',
        'user_id',
        'brand',
        'is_sold',
    ];

    // ✅ 画像URLを統一的に処理するアクセサー
    public function getImageUrlAttribute()
    {
        if (!$this->image_path) {
            return null;
        }

        // URLかどうかを判定
        if (filter_var($this->image_path, FILTER_VALIDATE_URL)) {
            // S3などの外部URL（シーダーデータ）
            return $this->image_path;
        } else {
            // ローカルストレージ（新規出品データ）
            return Storage::url($this->image_path);
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'item_category');
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function purchase()
    {
        return $this->hasOne(Purchase::class);
    }
}