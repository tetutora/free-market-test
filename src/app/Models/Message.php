<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = ['purchase_id', 'sender_id', 'body', 'image_path', 'is_read',];
    protected $casts = ['is_read' => 'boolean',];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function sendMessage($purchaseId, $senderId, $body, $image = null)
    {
        $message = new self();
        $message->purchase_id = $purchaseId;
        $message->sender_id = $senderId;
        $message->body = $body;

        if ($image) {
            $path = $image->store('messages', 'public');
            $message->image_path = $path;
        }

        $message->save();

        return $message;
    }
}
