<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Profile extends Model
{
    use HasFactory;

    protected $fillable =
    [
        'user_id','profile_picture','name','zipcode','address','building'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function updateOrCreateForUser($user, $data, $file = null)
    {
        $profile = $user->profile ?? new self(['user_id' => $user->id]);

        $profile->fill([
            'name' => $data['name'] ?? $user->name,
            'zipcode' => $data['zipcode'] ?? '',
            'address' => $data['address'] ?? '',
            'building' => $data['building'] ?? '',
        ]);

        if ($file) {
            if ($profile->profile_picture && Storage::exists('public/' . $profile->profile_picture)) {
                Storage::delete('public/' . $profile->profile_picture);
            }
            $profile->profile_picture = $file->store('profiles', 'public');
        }

        $profile->save();

        return $profile;
    }

    public function updateAddress(array $data)
    {
        $this->fill([
            'zipcode' => $data['zipcode'] ?? '',
            'address' => $data['address'] ?? '',
            'building' => $data['building'] ?? '',
        ]);

        $this->save();
    }


}
