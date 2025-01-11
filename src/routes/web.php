<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
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
    return redirect('/');
})->name('logout');

// 商品関連 (ログイン必須)
Route::middleware(['auth'])->group(function () {
    Route::post('/sell', [ProductController::class, 'store'])->name('products.store');
    Route::post('/product/{product}/favorite', [ProductController::class, 'toggleFavorite'])->name('product.favorite');
});

// コメント投稿 (ログイン必須)
Route::middleware(['auth'])->post('/product/{id}/comment', [CommentController::class, 'store'])->name('product.comment');

// プロフィール関連 (ログイン必須)
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/mypage', [ProfileController::class, 'myPage'])->name('profile.mypage');
    Route::get('/mypage/address/edit', [ProfileController::class, 'editAddress'])->name('profile.address.edit');
    Route::post('/mypage/address/edit', [ProfileController::class, 'updateAddress'])->name('profile.address.update');
});

// 購入関連 (ログイン必須)
Route::middleware(['auth'])->group(function () {
    Route::get('/purchase/{productId}', [PurchaseController::class, 'show'])->name('purchase.show');
    Route::post('/purchase/complete/{productId}', [PurchaseController::class, 'complete'])->name('purchase.complete');
    Route::get('/purchase/address/{item_id}', [AddressController::class, 'edit'])->name('purchase.address.edit');
    Route::post('/purchase/address/{item_id}', [AddressController::class, 'update'])->name('purchase.address.update');
});

// 商品一覧 (非ログインでも可)
Route::get('/products', [ProductController::class, 'index'])->name('products.index');

// 商品出品ページ (ログインしている場合)
Route::get('/sell', function () {
    if (Auth::check()) {
        return view('products.create');
    }
    return redirect()->route('login');
})->name('sell');

// 商品出品ページ (ログイン状態に応じて処理を分岐)
Route::get('/sell', function () {
    if (Auth::check()) {
        return app(ProductController::class)->create();
    }
    return redirect()->route('login');
})->name('sell');

Route::middleware(['auth'])->get('/products/liked', [ProductController::class, 'likedProducts'])->name('products.liked');

Route::post('/products/{product}/toggle-favorite', [ProductController::class, 'toggleFavorite'])->name('product.toggleFavorite');

Route::post('products/{id}/toggle-favorite', [ProductController::class, 'toggleFavorite'])->name('product.favorite');

