<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class availability extends Model
{
    protected $fillable=[
        'doctor_id',
        'date_availabilities',
        'start_hour',
        'end_hour',
        'status'    
    ];
    use HasFactory;

    public function doctors()
    {
        return $this->belongsTo(doctor::class,'doctor_id','id');
    }    

    public function appointments(){
        return $this->hasMany(appointment::class);
    }

}
