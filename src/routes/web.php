<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;

// トップページで商品一覧を表示
Route::get('/', [ProductController::class, 'index'])->name('home');
// マイリスト付き商品一覧画面
Route::get('/?tab=myList', [ProductController::class, 'index']);
// 商品詳細を表示
Route::get('/item/{item_id}', [ProductController::class, 'show']);
// 商品出品画面を表示
Route::get('/sell', [ProductController::class, 'create'])->name('sell');
// 商品出品処理を実行
Route::post('/sell', [ProductController::class, 'store']);

// 会員登録画面を表示
Route::get('/register', [RegisterController::class, 'showRegistrationForm']);
// 会員登録処理を実行
Route::post('/register', [RegisterController::class, 'register']);

// ログイン画面を表示
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
// ログイン処理を実行
Route::post('/login', [LoginController::class, 'login']);

// 商品購入画面を表示
// Route::get('/purchase/{item_id}', [PurchaseController::class, 'showPurchaseForm']);
// 商品購入処理を実行
Route::post('/purchase/{item_id}', [PurchaseController::class, 'processPurchase']);

// 住所変更画面を表示
Route::get('/purchase/address/{item_id}', [AddressController::class,'edit']);
// 住所変更処理を実行
Route::post('/purchase/address/{item_id}', [AddressController::class,'update']);

// プロフィールページ（ログイン済みのみアクセス可能）
Route::middleware(['auth'])->group(function () {
    // プロフィール画面を表示
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    // プロフィール編集処理を実行
    Route::post('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
    // 購入した商品一覧を表示
    Route::get('/mypage?tab=buy', [ProfileController::class, 'showPurchases']);
    // 出品した商品一覧を表示
    Route::get('/mypage?tab=sell', [ProfileController::class, 'showSales']);
});

// 住所編集ページ
Route::middleware(['auth'])->group(function () {
    Route::get('/mypage/address/edit', [ProfileController::class, 'editAddress'])->name('profile.address.edit');
    Route::post('/mypage/address/edit', [ProfileController::class, 'updateAddress'])->name('profile.address.update');
});

// 商品関連
// Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('product.show');

Route::post('/products/{id}/comment', [ProductController::class, 'addComment'])->name('product.comment');

// 商品購入画面を表示
Route::get('/purchase/{product}', [PurchaseController::class, 'show'])->name('purchase.show');

// 購入処理を実行
Route::post('/purchase/{product}', [PurchaseController::class, 'complete'])->name('purchase.complete');