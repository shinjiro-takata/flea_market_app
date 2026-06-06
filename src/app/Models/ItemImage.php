<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'image_path',
        'sort_order',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    // =============== アクセッサ ===============

    /**
     * 画像の表示用 URL を取得
     * 外部URL、ローカルストレージ、デフォルト画像を自動判定
     */
    public function getImageSrcAttribute(): string
    {
        if (\Illuminate\Support\Str::startsWith($this->image_path, ['http://', 'https://'])) {
            return $this->image_path;
        }

        return asset('storage/' . $this->image_path);
    }
}
