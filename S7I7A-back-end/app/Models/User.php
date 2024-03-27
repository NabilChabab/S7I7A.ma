<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Doctors;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends Authenticatable implements HasMedia
{
    use HasApiTokens, HasFactory, Notifiable , SoftDeletes , InteractsWithMedia;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }


    public function hasRole($role)
    {
        return $this->roles()->where('name', $role)->exists();
    }

    public function doctor()
    {
        return $this->hasOne(Doctor::class);
    }

    public function appointments(){
        return $this->hasMany(Appointment::class);
    }

    public function messages(){
        return $this->hasMany(Message::class);
    }
}
