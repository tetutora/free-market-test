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
Route::get('/', [ProductController::class, 'index'])->name('home');                      // 商品一覧画面（トップ画面）
Route::get('/products/mylist', [ProductController::class, 'index'])->name('home.mylist'); // 商品一覧画面（トップ画面）_マイリスト

// 会員登録関連
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');  // 会員登録画面
Route::post('/register', [RegisterController::class, 'register']);                               // 会員登録処理

// ログイン関連
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');                   // ログイン画面
Route::post('/login', [LoginController::class, 'login']);                                     // ログイン処理
Route::post('/logout', function () {                                                          // ログアウト処理
    Auth::logout();
    return redirect('/');
})->name('logout');

// 商品詳細関連
Route::get('/item/{item_id}', [ProductController::class, 'show'])->name('product.show');        // 商品詳細画面

// 商品コメント関連
Route::post('/products/{id}/comment', [ProductController::class, 'addComment'])->name('product.comment');  // コメント投稿

// 商品購入関連 (ログイン必須)
Route::middleware(['auth'])->group(function () {
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'show'])->name('purchase.show');  // 商品購入画面
    Route::post('/purchase/complete/{item_id}', [PurchaseController::class, 'complete'])->name('purchase.complete'); // 購入処理

    // 住所関連のルートを修正
    Route::get('/profile/address/{item_id}', [ProfileController::class, 'edit'])->name('profile.address.edit'); // 送付先住所変更画面
    Route::post('/profile/address/{item_id}', [ProfileController::class, 'update'])->name('profile.address.update');  // 送付先住所変更処理
});

// 商品出品関連 (ログイン必須)
Route::middleware(['auth'])->group(function () {
    Route::get('/sell', function () {
        return app(ProductController::class)->create();                                       // 商品出品画面
    })->name('sell');
    Route::post('/sell', [ProductController::class, 'store'])->name('products.store');         // 商品出品処理
});

// プロフィール関連 (ログイン必須)
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');              // プロフィール画面
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');    // プロフィール編集画面（設定画面）
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update'); // プロフィール更新処理
    Route::get('/mypage', [ProfileController::class, 'myPage'])->name('profile.mypage');       // プロフィール画面_購入した商品一覧
    Route::get('/mypage/profile', [ProfileController::class, 'myPage'])->name('profile.page');  // プロフィール画面_出品した商品一覧
});

// 商品いいね関連 (ログイン必須)
Route::middleware(['auth'])->get('/products/liked', [ProductController::class, 'likedProducts'])->name('products.liked'); // いいねした商品の一覧表示
Route::post('/products/{product}/toggle-favorite', [ProductController::class, 'toggleFavorite'])->name('product.toggleFavorite'); // いいね・取り消し処理
Route::post('/products/{id}/toggle-favorite', [ProductController::class, 'toggleFavorite'])->name('product.favorite')->middleware('auth'); // いいね・取り消し処理
Route::patch('/products/{product}/toggle-favorite', [ProductController::class, 'toggleFavorite']);                                // いいね・取り消し処理

// マイページ関連 (ログイン必須)
Route::get('/my-purchases', [PurchaseController::class, 'myPurchases'])->name('my-purchases'); // マイページ_購入した商品一覧
Route::get('/mypage', [ProfileController::class, 'myPage'])->name('profile.mypage');       // マイページ_出品した商品一覧
