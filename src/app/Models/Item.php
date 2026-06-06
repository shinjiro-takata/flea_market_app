<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'name',
        'description',
        'price',
        'status',
        'brand_name',
        'condition',
    ];

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function images()
    {
        return $this->hasMany(ItemImage::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function order()
    {
        return $this->hasOne(Order::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'item_category');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function getCommentsCountAttribute()
    {
        return $this->comments()->count();
    }

    /**
     * 最初の画像の表示用 URL を取得
     * 外部URL、ローカルストレージ、デフォルト画像を自動判定
     */
    public function getFirstImageSrcAttribute(): string
    {
        if ($this->images->isEmpty()) {
            return asset('images/no-image.png');
        }

        $imagePath = $this->images->first()->image_path;

        if (\Illuminate\Support\Str::startsWith($imagePath, ['http://', 'https://'])) {
            return $imagePath;
        }

        return asset('storage/' . $imagePath);
    }
}
