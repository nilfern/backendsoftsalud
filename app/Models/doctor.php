<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class doctor extends Model
{     
    protected $fillable=[        
        'name',
        'surname',
        'dni',
        'email',
        'password',
        'genre',
        'photo',
        'phone',
        'birthdate',
        'address',       
        'user_id',
        'specialty_id'    
    ];
    use HasFactory;

    public function users()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
    public function specialties()
    {
        return $this->belongsTo(specialty::class,'specialty_id','id');
    }
    public function appointments(){
        return $this->hasMany(appointment::class);
    }
    public function availabilities(){
        return $this->hasMany(availability::class);
    }

}
