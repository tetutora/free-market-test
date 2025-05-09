<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChatMessageRequest;
use App\Models\Purchase;
use App\Models\Message;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function show($id)
    {
        $transaction = Purchase::with(['product.user', 'user', 'messages'])->findOrFail($id);

        // 現在のユーザーのIDを取得
        $currentUserId = auth()->id();
        $buyer = $transaction->user;
        $seller = $transaction->product->user;

        $otherUser = $currentUserId === $buyer->id ? $seller : $buyer;

        $messages = $transaction->messages;

        // ここで送信者自身のメッセージには既読処理を行わないように修正
        $messages->where('is_read', false)->where('sender_id', '!=', $currentUserId)->each(function ($message) {
            $message->is_read = true;
            $message->save();
        });

        // 他の取引を取得
        $otherTransactions = Purchase::where('status', 'trading')
            ->where(function ($query) use ($otherUser) {
                $query->where('user_id', $otherUser->id)
                    ->orWhere('seller_id', $otherUser->id);
            })
            ->where('id', '!=', $transaction->id)
            ->with('product')
            ->get();

        // ビューにデータを渡す
        return view('transaction.show', [
            'transaction' => $transaction,
            'otherUser' => $otherUser,
            'otherTransactions' => $otherTransactions,
        ]);
    }

    public function sendMessage(ChatMessageRequest $request, $transactionId)
    {
        $validated = $request->validated();

        $message = new Message();
        $message->purchase_id = $transactionId;
        $message->sender_id = auth()->id();
        $message->body = $request->input('body');

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('messages', 'public'); // 保存先: storage/app/public/messages
            $message->image_path = $path;
        }

        $message->save();

        return redirect()->route('transaction.show', $transactionId);
    }
}
