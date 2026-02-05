<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'profile_picture',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = ['profile_picture_url'];

    // Accessor for profile picture URL
    public function getProfilePictureUrlAttribute()
    {
        if (!$this->profile_picture) {
            // Return default avatar if no picture
            return asset('images/default-avatar.png');
        }
        
        // Check if file exists in public directory
        if (file_exists(public_path($this->profile_picture))) {
            return asset($this->profile_picture);
        }
        
        // Return default if file doesn't exist
        return asset('images/default-avatar.png');
    }
}