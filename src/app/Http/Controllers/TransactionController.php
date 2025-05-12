<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChatMessageRequest;
use App\Mail\TransactionCompleted;
use App\Models\Message;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;


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

        $messages = $transaction->messages;

        $transaction->markMessagesAsReadForUser($currentUserId);

        $otherTransactions = $transaction->getOtherTransactions($otherUser);

        $isSeller = auth()->id() === $transaction->product->user_id;
        $showRatingModalForSeller = $isSeller && $transaction->isBuyerRated() && !$transaction->isSellerRated();

        return view('transaction.show', [
            'transaction' => $transaction,
            'otherUser' => $otherUser,
            'otherTransactions' => $otherTransactions,
            'showRatingModalForSeller' => $showRatingModalForSeller,
            'messages' => $messages,
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
            $this->sendCompletionEmailToSeller($transaction);
        }

        return response()->json(['success' => true, 'message' => '評価が送信されました']);
    }

    /**
     * 取引完了通知メールを出品者に送信
     */
    protected function sendCompletionEmailToSeller($transaction)
    {
        $seller = $transaction->product->user;

        Mail::to($seller->email)->send(new TransactionCompleted($transaction));
    }

    /**
     * 送信済みメッセージを編集
     */
    public function editMessage(Request $request, $messageId)
    {
        $message = Message::findOrFail($messageId);

        if ($message->sender_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => '権限がありません'], 403);
        }

        $message->body = $request->input('body');
        $message->save();

        return response()->json(['success' => true, 'message' => 'メッセージが更新されました']);
    }

    /**
     * 送信済みメッセージを削除
     */
    public function deleteMessage($messageId)
    {
        $message = Message::findOrFail($messageId);

        if ($message->sender_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => '権限がありません'], 403);
        }

        $message->delete();

        return response()->json(['success' => true, 'message' => 'メッセージが削除されました']);
    }
}