<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\{
    RegisterController,
    LoginController,
    ProfileController,
    ProductController,
    PurchaseController,
    VerificationController,
    StripeController,
    TransactionController,
};

// トップページ（商品一覧画面）
Route::get('/', [ProductController::class, 'index'])->name('home');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');

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
    Route::get('/purchase/success', [PurchaseController::class, 'purchase'])->name('purchase.success');
    Route::get('/purchase/cancel', [PurchaseController::class, 'cancel'])->name('purchase.cancel');
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'show'])->name('purchase.show');
    Route::post('/purchase/{item_id}', [PurchaseController::class, 'purchase'])->name('products.purchase');

    // 送付先住所変更
    Route::get('/purchase/address/{item_id}', [ProfileController::class, 'editAddress'])->name('profile.address.edit');
    Route::post('/purchase/address/{item_id}', [ProfileController::class, 'updateAddress'])->name('profile.address.update');
});

// 商品出品関連 (ログイン必須)
Route::middleware(['auth'])->group(function () {
    Route::get('/sell', [ProductController::class, 'create'])->name('sell');
    Route::post('/sell', [ProductController::class, 'store'])->name('products.store');
});

// プロフィール関連 (ログイン必須)
Route::middleware(['auth'])->group(function () {
    Route::get('/mypage', [ProfileController::class, 'myPage'])->name('profile.mypage');
    Route::get('/mypage/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/mypage/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/mypage/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    // マイリスト
    Route::get('/mypage/mylist', [ProductController::class, 'likedProducts'])->name('profile.mylist');

    // 購入履歴と出品商品
    Route::get('/mypage/purchases', [PurchaseController::class, 'myPurchases'])->name('profile.purchases');
    Route::get('/mypage/sells', [ProductController::class, 'mySells'])->name('profile.sells');
});

// いいね機能 (ログイン必須)
Route::middleware(['auth'])->group(function () {
    Route::post('/products/{product}/toggle-favorite', [ProductController::class, 'toggleFavorite'])->name('product.toggleFavorite');
});

// メール認証関連
Route::get('/email/verify', [VerificationController::class, 'show'])->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->middleware(['signed'])
    ->name('verification.verify');
Route::post('/email/verification-notification', [VerificationController::class, 'resend'])
    ->middleware(['auth'])
    ->name('verification.resend');

// Stripe決済関連
Route::post('/create-checkout-session', [StripeController::class, 'createCheckoutSession'])->name('stripe.checkout');

// 取引詳細画面（取引IDを基に詳細を表示）
Route::get('/transaction/{transaction}', [TransactionController::class, 'show'])->name('transaction.show');
Route::post('/transaction/{id}/send-message', [TransactionController::class, 'sendMessage'])->name('transaction.sendMessage');
Route::post('/transactions/{transactionId}/rate', [TransactionController::class, 'rate'])->name('transaction.submitRating');
Route::post('/transactions/messages/{messageId}/edit', [TransactionController::class, 'editMessage'])->name('transaction.editMessage');
Route::delete('/transactions/messages/{messageId}/delete', [TransactionController::class, 'deleteMessage'])->name('transaction.deleteMessage');