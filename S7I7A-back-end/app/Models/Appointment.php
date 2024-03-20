<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;


    protected $fillable = [
        'doctor_id', 'user_id', 'appointment_date', 'appointment_hour' , 'status' , 'type'
    ];


    public function doctor(){
        return $this->belongsTo(Doctors::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
