<?php

namespace App\Mail;

use App\Models\Purchase;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TransactionCompleted extends Mailable
{
    use Queueable, SerializesModels;

    public $transaction;

    public function __construct(Purchase $purchase)
    {
        $this->transaction = $purchase;
    }

    public function build()
    {
        return $this->subject('取引完了のお知らせ')
                    ->view('emails.transaction.completed');
    }
}