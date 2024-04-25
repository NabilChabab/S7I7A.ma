<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Prescription extends Model implements HasMedia
{
    use HasFactory , InteractsWithMedia;


    protected $fillable = [
        'medication',
        'dosage',
        'instructions',
        'appointment_id'
    ];





    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}
