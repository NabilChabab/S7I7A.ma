<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Article extends Model implements HasMedia
{
    use HasFactory , InteractsWithMedia , SoftDeletes;


    protected $fillable = [
        'category_id',
        'createdBy',
        'title',
        'content',
        'status'
    ];



    public function category(){
        return $this->belongsTo(Category::class);
    }
}
