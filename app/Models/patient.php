<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class patient extends Model
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
        'occupation',       
        'user_id'
    
    ];
    use HasFactory;

    public function users()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function appointments(){
        return $this->hasMany(appointment::class);
    }

}
