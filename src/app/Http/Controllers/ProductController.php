<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Status;
use App\Models\Comment;
use Illuminate\Http\Request;


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
        return view('products.show', compact('product'));
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
    // 画像のアップロード
    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('product_images', 'public');
    }

    // 商品を作成
    $product = Product::create([
        'name' => $request->name,
        'price' => $request->price,
        'description' => $request->description,
        'image' => $imagePath,
        'status_id' => $request->status_id,
        'user_id' => auth()->id(), // ログインユーザーID
    ]);

    // カテゴリを多対多で関連付け
    if ($request->has('category_ids')) {
        $product->categories()->sync($request->category_ids); // 複数のカテゴリIDを渡す
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
            'user_id' => auth()->id(), // ログインユーザーのID
            'content' => $request->comment,
        ]);

        return redirect()->route('product.show', $product->id)->with('success', 'コメントを投稿しました！');
    }
}
