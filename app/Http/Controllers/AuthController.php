<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
  
  

    public function login(Request $request)
    {
     
      // Comprobamos que el email y la contraseña han sido introducidos
         $request->validate(['email' => 'required','password' => 'required']);

                      
            if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid credentials'], 401);
            }
                   
            $user= User::where('email',$request->email)->first();        

            if ($user->role=="empleado"){
             $user = $user->load('employees');
            }
            if ($user->role=="administrador"){
             $user = $user->load('employees');
            }
            if ($user->role=="medico"){
             $user = $user->load('doctors');
            }

            $token = $user->createToken('authToken')->plainTextToken;

       return response()->json(['data'=>$user,'token' => $token,'rol' => $user->role], 200);
      


    }


    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out'], 200);   
    }


}
