<?php

namespace App\Http\Controllers;

use App\Models\specialty;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use Illuminate\Support\Facades\Validator;
use App\Custom\ResultResponse;

class SpecialtyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $specialty = specialty::paginate(100); 
        $resultResponse=new ResultResponse();
        $resultResponse->setData($specialty);
        $resultResponse->setStatusCode(ResultResponse::SUCCESS_CODE);
        $resultResponse->setMessage(ResultResponse::TXT_SUCCESS_CODE);  
        return response()->json($specialty);


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

          $mensaje=$this->validateSpecialty($request); 
          //   
           if($mensaje['estado']){ 

             $newSpecialty=new specialty([
                  'name'=>$request->get('name'),
                 
                   ]);

              $newSpecialty->save(); 
            
              $resultResponse->setData($newSpecialty);
             $resultResponse->setStatusCode(ResultResponse::SUCCESS_CODE);
             $resultResponse->setMessage(ResultResponse::TXT_SUCCESS_CODE);
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
    public function show(specialty $specialty)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(specialty $specialty)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, specialty $specialty)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(specialty $specialty)
    {
        //
    }

    private function validateSpecialty(Request $request){


        $rules=[
            'name'=>'required|string',   
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
