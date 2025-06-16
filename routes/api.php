<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\SpecialtyController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AvailabilityDoctorController;



Route::controller(AuthController::class)->prefix('login')->group(function(){
    Route::post('/','login');
      
});





Route::middleware(['auth:sanctum'])->group(function(){

    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    
       if ($request->user()->role=="administrador"){
        $user = $request->user()->load('employees'); // carga la relación
       }
       if ($request->user()->role=="empleado"){
        $user = $request->user()->load('employees'); // carga la relación
       }
       if ($request->user()->role=="medico"){
       $user = $request->user()->load('doctors'); // carga la relación
       }
        return response()->json($user);

    });    

    Route::controller(EmployeeController::class)->prefix('employee')->group(function(){
        Route::get('/','index');
        Route::post('/','store');
        Route::post('/{id}','update');    
        Route::get('/{id}','show');
        Route::get('/showbyid/{id}','showbyid');
        Route::get('/byuser/{id}','showbyuser');
        Route::delete('/{id}','destroy');    
    });

    Route::controller(PatientController::class)->prefix('patient')->group(function(){
       Route::get('/','index');
       Route::post('/','store');
       Route::patch('/{id}','update');    
       Route::get('/{id}','show');
       Route::delete('/{id}','destroy');

    });

    Route::controller(DoctorController::class)->prefix('doctor')->group(function(){
       Route::get('/','index');
       Route::post('/','store');
       Route::patch('/{id}','update');    
       Route::get('/{id}','show');
       Route::get('/byuser/{id}','showbyuser');
       Route::delete('/{id}','destroy');

    });

    Route::controller(SpecialtyController::class)->prefix('specialty')->group(function(){
       Route::get('/','index');
       Route::post('/','store');
       Route::patch('/{id}','update');    
       Route::get('/{id}','show');
       Route::delete('/{id}','destroy');

    });

    Route::controller(AppointmentController::class)->prefix('appointment')->group(function(){
       Route::get('/','index');
       Route::post('/','store');
       Route::patch('/{id}','update');    
       Route::get('/{id}','show');
       Route::delete('/{id}','destroy');
       Route::get('/appointmentbydoctor/{id}/{date}','appointmentbydoctor');
       Route::get('/appointmentall/{date}','appointmentall');
       Route::get('/appointmentbydoctorcount/{id}/{date}','appointmentbydoctorcount');
       Route::get('/appointmentallcount/{date}','appointmentallcount');
       Route::get('/attendappointment/{id}','attendappointment');
       Route::get('/cancelappointment/{id}','cancelappointment');
    
    });

   

    Route::controller(AvailabilityDoctorController::class)->prefix('availabilitydoctor')->group(function(){
       Route::get('/','index');
       Route::post('/','store');
       Route::patch('/{id}','update');    
       Route::get('/{id}/{date}','show');
       Route::get('/availability/{id}/{date}','showavailability');
       Route::get('/availabilityspecialty/{id}/{date}','showavailabilitySpecialty');
       Route::delete('/{id}','destroy');
    });

    Route::controller(AuthController::class)->prefix('logout')->group(function(){
       Route::post('/','logout');      
    });



});