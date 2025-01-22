<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_picture', 'name', 'zipcode', 'address', 'building'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($profile) {
            if (!$profile->user_id) {
                $profile->user_id = auth()->id();
            }
        });
    }
}
