<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\CommentController;


// トップページで商品一覧を表示
Route::get('/', [ProductController::class, 'index'])->name('home');
// 商品詳細を表示
Route::get('/item/{item_id}', [ProductController::class, 'show'])->name('product.show');
// 商品出品画面を表示
Route::get('/sell', [ProductController::class, 'create'])->name('sell');
// 商品出品処理を実行
Route::post('/sell', [ProductController::class, 'store'])->name('products.store');

// 会員登録画面を表示
Route::get('/register', [RegisterController::class, 'showRegistrationForm']);
// 会員登録処理を実行
Route::post('/register', [RegisterController::class, 'register']);

// ログイン画面を表示
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
// ログイン処理を実行
Route::post('/login', [LoginController::class, 'login']);

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
    // マイページ表示
    Route::get('/mypage', [ProfileController::class, 'mypage'])->name('profile.mypage');
    // 購入した商品一覧を表示
    Route::get('/mypage?tab=buy', [ProfileController::class, 'showPurchases']);
    // 出品した商品一覧を表示
    Route::get('/mypage?tab=sell', [ProfileController::class, 'showSales']);
    // 住所編集ページ
    Route::get('/mypage/address/edit', [ProfileController::class, 'editAddress'])->name('profile.address.edit');
    Route::post('/mypage/address/edit', [ProfileController::class, 'updateAddress'])->name('profile.address.update');
});
Route::post('/sell', [ProductController::class, 'store'])->middleware('auth')->name('products.store');


// 商品購入画面を表示
Route::get('/purchase/{product}', [PurchaseController::class, 'show'])->name('purchase.show');
// 購入処理を実行
Route::post('/purchase/{product}', [PurchaseController::class, 'complete'])->name('purchase.complete');

// ログアウト処理
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/');  // 商品一覧ページにリダイレクト
})->name('logout');

// プロフィール編集ページ
Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

Route::resource('products', ProductController::class);

Route::match(['POST', 'PUT'], '/profile/update', [ProfileController::class, 'update'])->name('profile.update');

Route::get('/profile/mypage', [ProfileController::class, 'myPage'])->name('profile.mypage');
Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.show');

Route::post('/product/{product}/comment', [CommentController::class, 'store'])->name('product.comment');

// 商品詳細表示
Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.show');

// コメント投稿
Route::post('/product/{id}/comment', [ProductController::class, 'addComment'])->name('product.comment');
// 商品詳細表示
Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.show');

// コメント投稿
Route::post('/product/{id}/comment', [CommentController::class, 'store'])->name('product.comment');

// コメント投稿用のルート
Route::post('/product/{id}/comment', [CommentController::class, 'store'])->name('product.comment');

Route::middleware('auth')->post('/product/{id}/comment', [CommentController::class, 'store'])->name('product.comment');
