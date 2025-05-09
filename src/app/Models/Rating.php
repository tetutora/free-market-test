<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'rating'];

    public function transaction()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
}
