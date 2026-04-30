<?php

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
Route::get('/purchase/{item_id}', [ScreenController::class, 'purchase'])->name('purchase.show');
Route::get('/purchase/address/{item_id}', [ScreenController::class, 'purchaseAddress'])->name('purchase.address');
Route::get('/sell', [ScreenController::class, 'sell'])->name('items.sell');
Route::post('/sell', [ScreenController::class, 'storeItem'])->name('items.store');
Route::get('/mypage', [ScreenController::class, 'mypage'])->name('mypage');
Route::get('/mypage/profile', [ScreenController::class, 'profile'])->name('mypage.profile');
