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

        $products = Product::query()
            ->when($search, function ($query) use ($search) {
                return $query->where('name', 'like', '%' . $search . '%');
            })
            ->get();

        return view('products.index', compact('products'));
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        $user = Auth::user();

        $profile = $user ? $user->profile : null;

        $zipcode = $profile ? $profile->zipcode : '未設定';
        $address = $profile ? $profile->address : '未設定';
        $building = $profile ? $profile->building : '未設定';

        return view('products.show', compact('product', 'zipcode', 'address', 'building'));
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

        // リダイレクト
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

    public function toggleFavorite(Product $product)
    {
        $user = Auth::user();

        $favorite = $product->favorites()->where('user_id', $user->id)->first();

        if ($favorite) {
            $favorite->delete();
        } else {
            Favorite::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
            ]);
        }

        return back();
    }
}
