<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChatMessageRequest;
use App\Models\Message;
use App\Models\Purchase;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * 商品取引中メッセージ画面
     */
    public function show($id)
    {
        $transaction = Purchase::with(['product.user', 'user', 'messages'])->findOrFail($id);

        $currentUserId = auth()->id();
        $buyer = $transaction->user;
        $seller = $transaction->product->user;

        $otherUser = $currentUserId === $buyer->id ? $seller : $buyer;

        $transaction->markMessagesAsReadForUser($currentUserId);

        $otherTransactions = $transaction->getOtherTransactions($otherUser);

        return view('transaction.show', [
            'transaction' => $transaction,
            'otherUser' => $otherUser,
            'otherTransactions' => $otherTransactions,
        ]);
    }

    /**
     * 取引中メッセージ送信処理
     */
    public function sendMessage(ChatMessageRequest $request, $transactionId)
    {
        $message = Message::sendMessage(
            $transactionId,
            auth()->id(),
            $request->input('body'),
            $request->file('image') ?? null
        );

        return redirect()->route('transaction.show', $transactionId);
    }
}
