
<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\{
    RegisterController,
    LoginController,
    ProfileController,
    ProductController,
    PurchaseController,
    VerificationsController
};

// トップページ関連
Route::get('/', [ProductController::class, 'index'])->name('home');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/mylist', [ProductController::class, 'index'])->name('home.mylist');

// 会員登録関連
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// ログイン関連
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');

// 商品詳細関連
Route::get('/item/{id}', [ProductController::class, 'show'])->name('product.show');  // 修正

// 商品コメント関連
Route::post('/products/{product}/add-comment', [ProductController::class, 'addComment'])->name('product.addComment');

// 商品購入関連 (ログイン必須)
Route::middleware(['auth'])->group(function () {
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'show'])->name('purchase.show');
    Route::post('/purchase/complete/{item_id}', [PurchaseController::class, 'complete'])->name('purchase.complete');

    // 住所関連
    Route::get('/profile/address/{item_id}', [ProfileController::class, 'edit'])->name('profile.address.edit');
    Route::post('/profile/address/{item_id}', [ProfileController::class, 'update'])->name('profile.address.update');
});

// 商品出品関連 (ログイン必須)
Route::middleware(['auth'])->group(function () {
    Route::get('/sell', [ProductController::class, 'create'])->name('sell');
    Route::post('/sell', [ProductController::class, 'store'])->name('products.store');
});

// プロフィール関連 (ログイン必須)
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
});

// マイページ関連 (ログイン必須)
Route::middleware(['auth'])->group(function () {
    Route::get('/mypage', [ProfileController::class, 'myPage'])->name('profile.mypage');  // 追加
});

// 商品いいね関連 (ログイン必須)
Route::middleware(['auth'])->group(function () {
    Route::get('/products/liked', [ProductController::class, 'likedProducts'])->name('products.liked');
    Route::post('/products/{product}/toggle-favorite', [ProductController::class, 'toggleFavorite'])->name('product.toggleFavorite');
});

// マイページ関連 (ログイン必須)
Route::middleware(['auth'])->group(function () {
    Route::get('/my-purchases', [PurchaseController::class, 'myPurchases'])->name('my-purchases');
});

// メール認証関連
Route::get('/email/verify', [VerificationsController::class, 'show'])->name('verification.notice'); // 通知ページ表示
Route::get('/email/verify/{id}/{hash}', [VerificationsController::class, 'verify'])
    ->middleware(['signed']) // 署名検証のみ
    ->name('verification.verify'); // 認証処理
Route::post('/email/verification-notification', [VerificationsController::class, 'resend'])
    ->middleware(['auth']) // ログインユーザーのみ再送可能
    ->name('verification.resend');

Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');
