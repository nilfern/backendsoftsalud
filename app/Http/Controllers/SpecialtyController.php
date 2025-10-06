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
     * Lista especialidades.  
     *  
     * Retorna la lista de especialidades registradas.
     */
    public function index()
    {
        $specialty = specialty::paginate(10);
        $resultResponse = new ResultResponse();
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
     * Registrar especialidad.
     * 
     * Registra los datos de la especialidad.
     */
    public function store(Request $request)
    {
        $resultResponse = new ResultResponse();
        try {

            $mensaje = $this->validateSpecialty($request);
            //   
            if ($mensaje['estado']) {

                $newSpecialty = new specialty([
                    'name' => $request->get('name'),

                ]);

                $newSpecialty->save();

                $resultResponse->setData($newSpecialty);
                $resultResponse->setStatusCode(ResultResponse::SUCCESS_CODE);
                $resultResponse->setMessage(ResultResponse::TXT_SUCCESS_CODE);
            } else {

                $resultResponse->setStatusCode(ResultResponse::ERROR_CODE);
                $resultResponse->setMessage($mensaje['errores']);
            }
        } catch (\Exception $e) {
            $resultResponse->setStatusCode(ResultResponse::ERROR_CODE);
            $resultResponse->setMessage(ResultResponse::TXT_ERROR_CODE + ":" + $e);
        }

        return response()->json($resultResponse);
    }

    /**
     * Consulta de especialidad
     * Retorna una especialidad consultada por su id.
     */
    public function show($id)
    {
        $resultResponse = new ResultResponse();
        try {
            $specialty = specialty::where('id', $id)->firstOrFail();
            $resultResponse->setData($specialty);
            $resultResponse->setStatusCode(ResultResponse::SUCCESS_CODE);
            $resultResponse->setMessage(ResultResponse::TXT_SUCCESS_CODE);
        } catch (\Exception $e) {
            $resultResponse->setStatusCode(ResultResponse::ERROR_ELEMENT_NOT_FOUNT_CODE);
            $resultResponse->setMessage(ResultResponse::TXT_ERROR_ELEMENT_NOT_FOUNT_CODE);
        }

        return response()->json($resultResponse);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(specialty $specialty)
    {
        //
    }

    /**
     * Actualizar especialidad.
     * Actualiza los datos de una especialidad especifica.
     */
    public function update(Request $request, $id)
    {

        $resultResponse = new ResultResponse();
        try {

            $specialty = specialty::where('id', $id)->first();
            $specialty->name = $request->get('name');
            $specialty->save();

            $resultResponse->setData($specialty);
            $resultResponse->setStatusCode(ResultResponse::SUCCESS_CODE);
            $resultResponse->setMessage(ResultResponse::TXT_SUCCESS_CODE);
        } catch (\Exception $e) {
            $resultResponse->setStatusCode(ResultResponse::ERROR_ELEMENT_NOT_FOUNT_CODE);
            $resultResponse->setMessage(ResultResponse::TXT_ERROR_ELEMENT_NOT_FOUNT_CODE);
        }
        return response()->json($resultResponse);
    }

    /**
     * Eliminar Especialidad
     * Borrar los datos de la especialidad por su id.
     */
    public function destroy($id)
    {
        $resultResponse = new ResultResponse();
        try {

            $specialty = specialty::findOrFail($id);
            $specialty->delete();
            $resultResponse->setData($specialty);
            $resultResponse->setStatusCode(ResultResponse::SUCCESS_CODE);
            $resultResponse->setMessage(ResultResponse::TXT_SUCCESS_CODE);
        } catch (\Exception $e) {
            $resultResponse->setStatusCode(ResultResponse::ERROR_ELEMENT_NOT_FOUNT_CODE);
            $resultResponse->setMessage(ResultResponse::TXT_ERROR_ELEMENT_NOT_FOUNT_CODE);
        }

        return response()->json($resultResponse);
    }

    private function validateSpecialty(Request $request)
    {


        $rules = [
            'name' => 'required|string',
        ];
        $messages = [
            'required' => 'El campo :attribute es requerido.',
            'integer' => 'El campo :attribute debe ser entero.',
            'exists' => 'El valor del campo :attribute es invalido.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return [
                'estado' => false,
                'errores' => $validator->errors()->all()
            ];
        } else {

            return [
                'estado' => true,
            ];
        }
    }
}
