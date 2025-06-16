<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\doctor;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use App\Custom\ResultResponse;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $doctor = doctor::with('users')->paginate(10); 
        $resultResponse=new ResultResponse();
        $resultResponse->setData($doctor);
        $resultResponse->setStatusCode(ResultResponse::SUCCESS_CODE);
        $resultResponse->setMessage(ResultResponse::TXT_SUCCESS_CODE);      
        return response()->json($doctor);
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
  public function store(Request $request)
    {            
        $resultResponse=new ResultResponse();
     try{
          $mensaje=$this->validateDoctor($request); 
          
         if($mensaje['estado']){ 

            $existe = doctor::where('dni', $request->get('dni'))->exists();
            if ($existe) {
                $resultResponse->setStatusCode(ResultResponse::ERROR_DNI_EXISTING_CODE);
                $resultResponse->setMessage(ResultResponse::TXT_ERROR_DNI_EXISTING_CODE);

            } else {
                $existe = User::where('email', $request->get('email'))->exists();
                if ($existe) {
                    $resultResponse->setStatusCode(ResultResponse::ERROR_EMAIL_EXISTING_CODE);
                    $resultResponse->setMessage(ResultResponse::TXT_ERROR_EMAIL_EXISTING_CODE);
                }else{
                          $newUser=new User([                
                            'email'=>$request->get('email'),
                            'password'=>bcrypt($request->get('password')),
                            'role'=>$request->get('role'),
                            'name'=>$request->get('name'),
                            'surname'=>$request->get('surname'),                 
                          ]);

                           $newUser->save(); 

                          if ($request->hasFile('photo')) {
                            $photo = $request->file('photo');
                            $photoPath = $photo->store('doctors_photos', 'public');
                          } else {
                           $photoPath = "doctors_photos/usuario.jpg";  
                          }     
            
                          $newDoctor=new doctor([
                            'name'=>$request->get('name'),
                            'surname'=>$request->get('surname'),
                            'dni'=>$request->get('dni'),
                            'genre'=>$request->get('genre'),
                            'photo'=>$photoPath,        
                            'phone'=>$request->get('phone'),                  
                            'email'=>$request->get('email'),
                            'password'=>bcrypt($request->get('password')), 
                            'birthdate'=>$request->get('birthdate'),
                            'address'=>$request->get('address'),                  
                            'user_id'=>$newUser->id,
                            'specialty_id'=>$request->get('specialty'),    
                          ]);
                           $newDoctor->save();
                           $resultResponse->setData($newDoctor);
                           $resultResponse->setStatusCode(ResultResponse::SUCCESS_CODE);
                           $resultResponse->setMessage(ResultResponse::TXT_SUCCESS_CODE);


                }
            
            }



         }else{
              $resultResponse->setStatusCode(ResultResponse::ERROR_CODE);
              $resultResponse->setMessage($mensaje['errores']);
              }          
        }catch(\Exception $e){
             $resultResponse->setStatusCode(ResultResponse::ERROR_CODE);
             $resultResponse->setMessage(ResultResponse::TXT_ERROR_CODE+":"+$e);               
        }
      return response()->json($resultResponse);
    }



    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $resultResponse=new ResultResponse();
        try{
             $doctor = Doctor::with('users')->where('dni', $id)->firstOrFail();
             $resultResponse->setData($doctor);
             $resultResponse->setStatusCode(ResultResponse::SUCCESS_CODE);
             $resultResponse->setMessage(ResultResponse::TXT_SUCCESS_CODE);
          
           }catch(\Exception $e){
            $resultResponse->setStatusCode(ResultResponse::ERROR_ELEMENT_NOT_FOUNT_CODE);
            $resultResponse->setMessage(ResultResponse::TXT_ERROR_ELEMENT_NOT_FOUNT_CODE);
           }

        return response()->json($resultResponse);

    }

    public function showbyuser($id)
    {
        $resultResponse=new ResultResponse();
        try{
           
          
             $doctor = Doctor::with('users')->where('user_id', $id)->firstOrFail();    
             $resultResponse->setData($doctor);
             $resultResponse->setStatusCode(ResultResponse::SUCCESS_CODE);
             $resultResponse->setMessage(ResultResponse::TXT_SUCCESS_CODE);
          
           }catch(\Exception $e){
            $resultResponse->setStatusCode(ResultResponse::ERROR_ELEMENT_NOT_FOUNT_CODE);
            $resultResponse->setMessage(ResultResponse::TXT_ERROR_ELEMENT_NOT_FOUNT_CODE);
           }        
        return response()->json($resultResponse);



    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(doctor $doctor)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, doctor $doctor)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(doctor $doctor)
    {
        //
    }

    private function validateDoctor(Request $request){


        $rules=[
            'name'=>'required|string',
            'surname'=>'required|string',
            'dni'=>'required|string',
            'email'=>'required|string',
            'password'=>'required|string',
            'genre'=>'required|string',       
            'phone'=>'required|string',                                       
       ];
        $messages = [
            'required' => 'El campo :attribute es requerido.',
            'integer' => 'El campo :attribute debe ser entero.',
            'exists' => 'El valor del campo :attribute es invalido.',          
       ];

       $validator = Validator::make($request->all(), $rules,$messages);

       if ($validator->fails()) {
        return ['estado'=>false,
              'errores'=>$validator->errors()->all()
             ];
       }else{
        
         return ['estado'=>true,
                 ];
       }
    }


}
