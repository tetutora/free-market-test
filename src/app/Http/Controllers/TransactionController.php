<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Message;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function show($id)
    {
        $transaction = Purchase::with(['product.user', 'user', 'messages'])->findOrFail($id);

        $currentUserId = auth()->id();
        $buyer = $transaction->user;
        $seller = $transaction->product->user;

        $otherUser = $currentUserId === $buyer->id ? $seller : $buyer;

        // 取引相手の他の取引を取得
        $otherTransactions = Purchase::where('user_id', $otherUser->id)
                                    ->where('id', '!=', $transaction->id)
                                    ->with('product')
                                    ->get();

        return view('transaction.show', [
            'transaction' => $transaction,
            'otherUser' => $otherUser,
            'otherTransactions' => $otherTransactions,  // 他の取引情報をビューに渡す
        ]);
    }

    public function sendMessage(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $transaction = Purchase::findOrFail($id);
        $message = new Message();
        $message->purchase_id = $transaction->id;
        $message->sender_id = auth()->id();
        $message->body = $request->message;
        $message->save();

        return redirect()->route('transaction.show', $transaction->id);
    }
}
