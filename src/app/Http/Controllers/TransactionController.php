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

        $isSeller = auth()->id() === $transaction->product->user_id;
        $showRatingModalForSeller = $isSeller && $transaction->isBuyerRated() && !$transaction->isSellerRated();

        return view('transaction.show', [
            'transaction' => $transaction,
            'otherUser' => $otherUser,
            'otherTransactions' => $otherTransactions,
            'showRatingModalForSeller' => $showRatingModalForSeller,
        ]);
    }

    /**
     * 取引中メッセージ送信処理
     */
    public function sendMessage(ChatMessageRequest $request, $transactionId)
    {
        $message = new Message();
        $message = $message->sendMessage(
            $transactionId,
            auth()->id(),
            $request->input('body'),
            $request->file('image') ?? null
        );

        return redirect()->route('transaction.show', $transactionId);
    }

    /**
     * 取引評価処理
     */
    public function rate(Request $request, $transactionId)
    {
        $transaction = Purchase::findOrFail($transactionId);

        $rating = $request->input('rating');
        $userId = auth()->id();

        if ($transaction->ratings()->where('user_id', $userId)->exists()) {
            return response()->json(['success' => false, 'message' => '既に評価済みです'], 400);
        }

        $transaction->ratings()->create([
            'user_id' => $userId,
            'rating' => $rating,
        ]);

        if ($transaction->isBuyerRated() && $transaction->isSellerRated()) {
            $transaction->status = 'completed';
            $transaction->save();
        }

        return response()->json(['success' => true, 'message' => '評価が送信されました']);
    }
}