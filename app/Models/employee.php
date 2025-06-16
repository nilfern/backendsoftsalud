<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class employee extends Model
{
  use HasApiTokens,HasFactory, Notifiable;

  protected $fillable=['name',
    'surname',
    'dni',
    'occupation',
    'gross_salary',
    'email',
    'password',
    'genre',
    'photo',
    'phone',
    'birthdate',
    'address',
    'user_id'
  ];
    use HasFactory;

    public function users()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

}
