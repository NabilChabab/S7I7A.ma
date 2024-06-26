<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Doctor extends Model implements HasMedia
{
    use HasFactory, SoftDeletes , InteractsWithMedia;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'doctors';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'address',
        'experience',
        'qualification',
        'description',
        'CIN',
        'price',
        'user_id',
        'category_id'
    ];

    /**
     * Get the user associated with the doctor.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function articles()
    {
        return $this->hasMany(Article::class, 'createdBy');
    }

    public function appointments(){
        return $this->hasMany(Appointment::class);
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }

}
