<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Product;

class CommentController extends Controller
{
    public function store(Request $request, $id)
    {
        // コメントのバリデーション
        $request->validate([
            'content' => 'required|string|max:255',
        ]);

        // Productモデルを取得
        $product = Product::findOrFail($id);

        // ログインユーザーのIDを取得
        $userId = auth()->id(); // auth()->id() でログインユーザーのIDを取得

        // user_idが取得できない場合はエラー処理を追加（ログインユーザーが必要）
        if (!$userId) {
            return redirect()->route('login')->with('error', 'ログインが必要です');
        }

        // コメントの作成
        $comment = new Comment();
        $comment->content = $request->content;
        $comment->product_id = $product->id;
        $comment->user_id = $userId; // ログインユーザーのIDを設定
        $comment->save();

        // 商品詳細ページにリダイレクト
        return redirect()->route('product.show', $product->id);
    }

}
