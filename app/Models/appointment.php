<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class appointment extends Model
{
    protected $fillable=[        
        'date_appointments',
        'start_hour',     
        'doctor_id',
        'patient_id',
        'availabily_id',
        'status'    
    ];
    use HasFactory;

    public function doctors()
    {
        return $this->belongsTo(doctor::class,'doctor_id','id');
    }  
    public function patients(){
        return $this->belongsTo(patient::class,'patient_id','id');
    }       
    public function availabilities(){
        return $this->belongsTo(availability::class,'availabily_id','id');
    }      

}
