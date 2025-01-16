<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Favorite;
use App\Http\Requests\ExhibitionRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    // 商品一覧表示
    public function index(Request $request)
    {
        $search = $request->get('search');
        $userId = Auth::id();

        $products = Product::query()
            ->when($search, function ($query) use ($search) {
                return $query->where('name', 'like', '%' . $search . '%');
            })
            ->when($userId, function ($query) use ($userId) {
                return $query->where('user_id', '!=', $userId);
            })
            ->get();

        $likedProducts = Auth::check() ? Auth::user()->favorites : collect([]);

        return view('products.index', compact('products', 'likedProducts'));
    }

    // 商品詳細表示
    public function show($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return abort(404, '商品が見つかりません');
        }

        $user = Auth::user();
        $isFavorited = $user ? $user->favorites()->where('product_id', $id)->exists() : false;
        $favoriteCount = $product->favorites()->count();

        return view('products.show', compact('product', 'isFavorited', 'favoriteCount'));
    }

    // コメント投稿
    public function addComment(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string|max:255',
        ]);

        $product = Product::findOrFail($id);

        Comment::create([
            'product_id' => $product->id,
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);

        return redirect()->route('product.show', $id);
    }

    // 商品出品ページ表示
    public function create()
    {
        $categories = Category::all();

        return view('products.create', compact('categories'));
    }

    // 商品保存処理
    public function store(Request $request)
    {
        $product = new Product([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'status' => $request->status,
            'user_id' => auth()->id(), // ログインユーザーID
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $product->image = $path;
        }

        $product->save();

        // カテゴリーの紐付けを正確に処理
        if ($request->has('category_id')) {
            $product->categories()->attach(explode(',', $request->category_id));
        }

        return redirect()->route('products.index');
    }

    // いいね機能
    public function toggleFavorite(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'ログインが必要です'], 401);
        }

        // いいねのトグル
        $user->favorites()->toggle($product->id);

        return response()->json([
            'favorited' => $user->favorites()->where('product_id', $id)->exists(),
            'favoriteCount' => $product->favorites()->count(),
        ]);
    }

    // いいねした商品一覧
    public function likedProducts()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $likedProducts = $user->favorites()->with('product')->get()->pluck('product');

        return view('products.mylist', compact('likedProducts'));
    }
}
