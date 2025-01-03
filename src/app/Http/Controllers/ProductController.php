<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Status;
use App\Models\Comment;
use App\Models\Favorite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;



class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        // 検索キーワードがあれば、それを基にデータを絞り込む
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
        $profile = $user->profile;

        // プロフィール画像のパスを設定
        $profile_picture = $profile && $profile->profile_picture
            ? asset('storage/' . $profile->profile_picture)
            : asset('images/default-profile.jpg'); // デフォルト画像

        return view('products.show', compact('product', 'profile_picture'));
    }

    // 商品出品画面の表示
    public function create()
    {
        // カテゴリを全て取得してフォームに渡す
        $categories = Category::all();
        $statuses = Status::all();

        return view('products.create', compact('categories', 'statuses'));
    }

    // 商品出品処理
    public function store(Request $request)
    {
        // 商品を作成
        $product = new Product([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'status_id' => $request->status_id,
            'user_id' => auth()->id(), // ログインユーザーID
        ]);

        // 画像がアップロードされた場合
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public'); // 画像を保存してパスを取得
            $product->image = $path; // パスをデータベースに保存
        }

        $product->save(); // 商品情報を保存

        if ($request->has('category_id')) {
            foreach ($request->category_id as $categoryId) {
                DB::table('products_categories')->insert([
                    'category_id' => $categoryId,
                    'product_id' => $product->id,
                    'user_id' => auth()->id(), // ログインユーザーのIDを挿入
                ]);
            }
        }

        // リダイレクト
        return redirect()->route('products.index')->with('success', '商品を出品しました！');
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
            'user_id' => auth()->id(), // ログインユーザーのID
            'content' => $request->comment,
        ]);

        return redirect()->route('product.show', $product->id)->with('success', 'コメントを投稿しました！');
    }

    public function toggleFavorite(Product $product)
    {
        $user = Auth::user();

        // お気に入りがすでに登録されているか確認
        $favorite = $product->favorites()->where('user_id', $user->id)->first();

        if ($favorite) {
            // 既にお気に入りに登録されている場合は削除
            $favorite->delete();
        } else {
            // お気に入りを追加
            Favorite::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
            ]);
        }

        return back();
    }

}
