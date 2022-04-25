<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 'email', 'password', 'avatar', 'address', 'phone', 'country', 'city'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    public function setPasswordAttribute($value)
    {
        if (Hash::needsRehash($value)) {
            $value = Hash::make($value);
        }
   
        $this->attributes['password'] = $value;
    }
    public function setAvatarAttribute($value)
    {
        if($value == null) {
            $this->attributes['avatar'] = 'default.png';
        } else {
            $this->attributes['avatar'] = update_image($this->avatar, $value, $this, 'users');
        }
    }

    /**
     * The rooms that belong to the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'users_rooms');
    }
    
    /**
     * The messages that belong to the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    
    public function friends() {
        return $this->belongsToMany(User::class, 'user_friends', 'user_id', 'friend_id');
    }

    
    public function check_friend($friend)
    {
        $this->friends()->attach($friend);
    }

    public function isFriendWith($user)
    {
        return (bool) $this->friends()->where('friend_id', $user->id)->count();
    }

    // check if user has a room with the friend
    public function hasRoomWith($friend)
    {
        return (bool) $this->rooms()->whereHas('users', function($query) use ($friend) {
            $query->where('user_id', $friend->id);
        })->count();
    }
}
