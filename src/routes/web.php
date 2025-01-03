<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    RegisterController,
    LoginController,
    ProfileController,
    ProductController,
    PurchaseController,
    AddressController,
    CommentController
};

// トップページ関連
Route::get('/', [ProductController::class, 'index'])->name('home');
Route::get('/item/{item_id}', [ProductController::class, 'show'])->name('product.show');

// 認証関連
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/'); // トップページにリダイレクト
})->name('logout');

// 商品関連（認証が必要な部分はmiddlewareを利用）
Route::middleware(['auth'])->group(function () {
    Route::get('/sell', [ProductController::class, 'create'])->name('sell');
    Route::post('/sell', [ProductController::class, 'store'])->name('products.store');
    Route::post('/product/{product}/favorite', [ProductController::class, 'toggleFavorite'])->name('product.favorite');
});

// コメント投稿
Route::middleware(['auth'])->post('/product/{id}/comment', [CommentController::class, 'store'])->name('product.comment');

// プロフィール関連
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/mypage', [ProfileController::class, 'myPage'])->name('profile.mypage');
    Route::get('/mypage/address/edit', [ProfileController::class, 'editAddress'])->name('profile.address.edit');
    Route::post('/mypage/address/edit', [ProfileController::class, 'updateAddress'])->name('profile.address.update');
});

// 購入関連
Route::middleware(['auth'])->group(function () {
    Route::get('/purchase/{product}', [PurchaseController::class, 'show'])->name('purchase.show');
    Route::post('/purchase/{product}', [PurchaseController::class, 'complete'])->name('purchase.complete');
    Route::get('/purchase/address/{item_id}', [AddressController::class, 'edit'])->name('purchase.address.edit');
    Route::post('/purchase/address/{item_id}', [AddressController::class, 'update'])->name('purchase.address.update');
});
