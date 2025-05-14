<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Http\Requests\ExhibitionRequest;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Favorite;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * 商品一覧画面表示
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $userId = Auth::id();

        $products = Product::searchExcludingUser($search, $userId);

        foreach ($products as $p) {
        \Log::info("Product: {$p->name}, is_sold: {$p->is_sold}");
    }

        $likedProducts = Auth::check() ? Auth::user()->favorites : collect([]);

        return view('products.index', compact('products', 'likedProducts', 'search'));
    }

    /**
     * 商品詳細画面表示
     */
    public function show($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return abort(404, '商品が見つかりません');
        }

        $user = Auth::user();
        $isFavorited = $user ? $user->favorites()->where('product_id', $id)->exists() : false;
        $favoriteCount = $product->favorites()->count();
        $commentCount = $product->comments()->count();

        return view('products.show', compact('product', 'isFavorited', 'favoriteCount', 'commentCount'));
    }

    /**
     * 商品へのコメント送信処理
     */
    public function addComment(CommentRequest $request, Product $product)
    {
        if (!Auth::check()) {
            return redirect()->route('products.show', $product->id)->with('error', 'ログインが必要です');
        }

        Comment::postFromRequest($product, Auth::id(), $request->content);

        return redirect()->route('products.show', $product->id);
    }

    /**
     * 商品出品画面表示
     */
    public function create()
    {
        $categories = Category::all();

        return view('products.create', compact('categories'));
    }

    /**
     * 出品商品保存処理
     */
    public function store(ExhibitionRequest $request)
    {
        Product::createFromRequest($request);

        return redirect()->route('products.index');
    }

    /**
     * いいね機能
     */
    public function toggleFavorite(Request $request, $id)
    {
        $user = Auth::user();
        $product = Product::findOrFail($id);

        if (!$user) {
            return response()->json(['message' => 'ログインが必要です'], 401);
        }

        return response()->json($product->toggleFavoriteByUser($user));
    }

    /**
     * いいねした商品一覧
     */
    public function likedProducts(Request $request)
    {
        $search = $request->get('search');
        $user = Auth::user();

        if(!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $likedProducts = Favorite::getLikedProducts($user, $search);

        return view('products.mylist', compact('likedProducts', 'search'));
    }
}
