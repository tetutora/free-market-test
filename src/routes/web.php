<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\{
    RegisterController,
    LoginController,
    ProfileController,
    ProductController,
    PurchaseController,
    VerificationController
};

// トップページ
Route::get('/', [ProductController::class, 'index'])->name('home');

// 商品詳細画面
Route::get('/item/{item_id}', [ProductController::class, 'show'])->name('products.show');

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

// 商品コメント投稿
Route::post('/products/{product}/add-comment', [ProductController::class, 'addComment'])->name('product.addComment');

// 商品購入関連 (ログイン必須)
Route::middleware(['auth'])->group(function () {
    // 商品購入完了後の処理
    Route::get('/purchase/success', [PurchaseController::class, 'success'])->name('purchase.success');

    // 商品購入画面
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'show'])->name('purchase.show');

    // 購入時住所変更画面
    Route::get('/profile/address/{item_id}', [ProfileController::class, 'editAddress'])->name('profile.address.edit');

    // 購入時住所変更処理
    Route::post('/profile/address/update/{item_id}', [ProfileController::class, 'updateAddress'])->name('profile.address.update');

    // 住所変更後商品購入画面
    Route::get('/products/{item_id}/purchase', [PurchaseController::class, 'show'])->name('products.purchase');
});

// 商品出品関連 (ログイン必須)
Route::middleware(['auth'])->group(function () {
    // 商品出品画面
    Route::get('/sell', [ProductController::class, 'create'])->name('sell');

    // 商品出品処理
    Route::post('/sell', [ProductController::class, 'store'])->name('products.store');
});

// プロフィール関連 (ログイン必須)
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');

    // プロフィール設定画面
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');

    // プロフィール設定処理
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
});

// マイページ関連 (ログイン必須)
Route::middleware(['auth'])->group(function () {
    Route::get('/mypage', [ProfileController::class, 'myPage'])->name('profile.mypage');
});

// 商品いいね (ログイン必須)
Route::middleware(['auth'])->group(function () {
    // いいねした商品をマイリストで表示
    Route::get('/products/liked', [ProductController::class, 'likedProducts'])->name('products.liked');

    // 商品へのいいね
    Route::post('/products/{product}/toggle-favorite', [ProductController::class, 'toggleFavorite'])->name('product.toggleFavorite');
});

// マイページ購入履歴 (ログイン必須)
Route::middleware(['auth'])->group(function () {
    Route::get('/my-purchases', [PurchaseController::class, 'myPurchases'])->name('my-purchases');
});

// メール認証関連
Route::get('/email/verify', [VerificationController::class, 'show'])->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->middleware(['signed'])
    ->name('verification.verify');
Route::post('/email/verification-notification', [VerificationController::class, 'resend'])
    ->middleware(['auth'])
    ->name('verification.resend');

// stripe決済関連
Route::post('/create-checkout-session', [StripeController::class, 'createCheckoutSession'])->name('stripe.checkout');

// 商品購入キャンセル
Route::get('/purchase/cancel', [PurchaseController::class, 'cancel'])->name('purchase.cancel');

// 商品購入処理
Route::post('/purchase/{item_id}', [PurchaseController::class, 'purchase'])->name('products.purchase');

// 商品一覧ページ
Route::get('/products', [ProductController::class, 'index'])->name('products.index');

Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

// routes/web.php

Route::middleware(['auth'])->group(function () {
    Route::get('/mypage/mylist', [ProductController::class, 'likedProducts'])->name('profile.mylist');
});
