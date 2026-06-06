<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ScreenController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [ScreenController::class, 'index'])->name('items.index');
Route::get('/item/{item_id}', [ScreenController::class, 'showItem'])->name('items.show');

// === 認証前のルート（guest ミドルウェア） ===
// 会員登録・ログイン時のみアクセス可能
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

// === メール認証関連ルート ===
// ログイン後、メール認証前にアクセス可能
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [VerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'confirm'])
        ->middleware('signed')
        ->name('verification.verify');
    Route::post('/email/verification-notification', [VerificationController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
    Route::post('/email/verify-quick', [VerificationController::class, 'verifyNow'])->name('verification.quick');
});

Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

// === 認証・メール認証完了後のルート ===
// ログイン＆メール認証完了が必須
Route::middleware('auth', 'verified')->group(function () {
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'show'])->name('purchase.show');
    Route::post('/purchase/{item_id}/payment', [PurchaseController::class, 'setPayment'])->name('purchase.setPayment');
    Route::get('/purchase/address/{item_id}', [ScreenController::class, 'purchaseAddress'])->name('purchase.address');
    Route::post('/purchase/address/{item_id}', [ScreenController::class, 'updatePurchaseAddress'])->name('purchase.address.update');
    Route::get('/sell', [ScreenController::class, 'sell'])->name('items.sell');
    Route::post('/sell', [ScreenController::class, 'exhibition'])->name('exhibition.store');
    Route::get('/mypage', [ScreenController::class, 'mypage'])->name('items.mypage');
    Route::get('/mypage/profile', [ScreenController::class, 'profile'])->name('mypage.profile');
    Route::post('/mypage/profile', [ScreenController::class, 'updateProfile'])->name('profile.update');
    Route::post('/item/{item_id}/like', [LikeController::class, 'toggle'])->name('like.toggle');
    Route::post('/item/{item_id}/comment', [CommentController::class, 'store'])->name('comment.store');
    Route::post('/purchase/{item_id}', [PurchaseController::class, 'store'])->name('purchase.store');
    Route::get('/purchase/{item_id}/success', [PurchaseController::class, 'success'])->name('purchase.success');
});
