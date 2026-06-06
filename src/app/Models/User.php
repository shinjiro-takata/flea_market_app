<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * 一括割り当て可能な属性
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_image',
    ];

    /**
     * シリアライズから除外する属性
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * 属性の型キャスト
     * email_verified_at: メール認証完了日時を DateTime として扱う
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // =============== リレーション ===============

    public function items()
    {
        return $this->hasMany(Item::class, 'seller_id');
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }

    public function salesOrders()
    {
        return $this->hasMany(Order::class, 'seller_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    // =============== アクセッサ ===============

    /**
     * プロフィール画像の表示用 URL を取得
     * 外部URL、ローカルストレージ、デフォルト画像を自動判定
     */
    public function getProfileImageSrcAttribute(): string
    {
        if (!$this->profile_image) {
            return asset('images/default-profile.svg');
        }

        if (\Illuminate\Support\Str::startsWith($this->profile_image, ['http://', 'https://'])) {
            return $this->profile_image;
        }

        return asset('storage/' . $this->profile_image);
    }
}
