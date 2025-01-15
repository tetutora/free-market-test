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

    public function show($id)
    {
        $product = Product::findOrFail($id);
        $user = Auth::user();

        // いいね状態とカウント
        $isFavorited = $user ? $user->favorites()->where('product_id', $id)->exists() : false;
        $favoriteCount = $product->favorites()->count();

        return view('products.show', compact('product', 'isFavorited', 'favoriteCount'));
    }

    public function create()
    {
        $categories = Category::all();

        return view('products.create', compact('categories'));
    }

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

        if ($request->has('category_id')) {
            $categoryIds = explode(',', $request->category_id);
            foreach ($categoryIds as $categoryId) {
                DB::table('products_categories')->insert([
                    'category_id' => $categoryId,
                    'product_id' => $product->id,
                    'user_id' => auth()->id(),
                ]);
            }
        }

        return redirect()->route('products.index');
    }

    public function addComment(Request $request, $id)
    {
        $request->validate([
            'comment' => 'required|string|max:500',
        ]);

        $product = Product::findOrFail($id);

        // コメントを保存
        Comment::create([
            'product_id' => $product->id,
            'user_id' => auth()->id(),
            'content' => $request->comment,
        ]);

        return redirect()->route('product.show', $product->id)->with('success', 'コメントを投稿しました！');
    }

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