<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\patient;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Custom\ResultResponse;

class PatientController extends Controller
{
  /**
   * Lista de pacientes
   * Retorna la lista de pacientes registrados.
   */
  public function index()
  {
    $patient = patient::with('users')->paginate(10);
    $resultResponse = new ResultResponse();
    $resultResponse->setData($patient);
    $resultResponse->setStatusCode(ResultResponse::SUCCESS_CODE);
    $resultResponse->setMessage(ResultResponse::TXT_SUCCESS_CODE);

    return response()->json($patient);
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    //
  }

  /**
   * Registrar paciente
   * Registra los datos del paciente.
   */
  public function store(Request $request)
  {

    $resultResponse = new ResultResponse();
    try {

      $mensaje = $this->validatePatient($request);

      if ($mensaje['estado']) {

        $existe = patient::where('dni', $request->get('dni'))->exists();
        if ($existe) {
          $resultResponse->setStatusCode(ResultResponse::ERROR_DNI_EXISTING_CODE);
          $resultResponse->setMessage(ResultResponse::TXT_ERROR_DNI_EXISTING_CODE);
        } else {
          $existe = User::where('email', $request->get('email'))->exists();
          if ($existe) {
            $resultResponse->setStatusCode(ResultResponse::ERROR_EMAIL_EXISTING_CODE);
            $resultResponse->setMessage(ResultResponse::TXT_ERROR_EMAIL_EXISTING_CODE);
          } else {

            $newUser = new User([
              'email' => $request->get('email'),
              'password' => bcrypt($request->get('password')),
              'role' => $request->get('role'),
              'name' => $request->get('name'),
              'surname' => $request->get('surname'),
            ]);

            $newUser->save();

            if ($request->hasFile('photo')) {
              $photo = $request->file('photo');
              $photoPath = $photo->store('patients_photos', 'public');
            } else {
              $photoPath = "patients_photos/usuario.jpg";
            }

            $newPatient = new patient([
              'name' => $request->get('name'),
              'surname' => $request->get('surname'),
              'dni' => $request->get('dni'),
              'genre' => $request->get('genre'),
              'photo' => $photoPath,
              'occupation' => $request->get('occupation'),
              'phone' => $request->get('phone'),
              'birthdate' => $request->get('birthdate'),
              'address' => $request->get('address'),
              'email' => $request->get('email'),
              'password' => bcrypt($request->get('password')),
              'user_id' => $newUser->id,
            ]);

            $newPatient->save();
            $resultResponse->setData($newPatient);
            $resultResponse->setStatusCode(ResultResponse::SUCCESS_CODE);
            $resultResponse->setMessage(ResultResponse::TXT_SUCCESS_CODE);
          }
        }
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
   * Retorna paciente por DNI
   * Retorna un paciente consultado por su dni.
   */
  public function show($id)
  {

    $resultResponse = new ResultResponse();
    try {
      $patient = Patient::with('users')->where('dni', $id)->firstOrFail();
      $resultResponse->setData($patient);
      $resultResponse->setStatusCode(ResultResponse::SUCCESS_CODE);
      $resultResponse->setMessage(ResultResponse::TXT_SUCCESS_CODE);
    } catch (\Exception $e) {
      $resultResponse->setStatusCode(ResultResponse::ERROR_ELEMENT_NOT_FOUNT_CODE);
      $resultResponse->setMessage(ResultResponse::TXT_ERROR_ELEMENT_NOT_FOUNT_CODE);
    }
    return response()->json($resultResponse);
  }

  /**
   * Retornar paciente por ID
   * Retorna un paciente consultado por su id.
   *  
   */
  public function showbyid($id)
  {

    $resultResponse = new ResultResponse();
    try {
      $patient = Patient::with('users')->where('id', $id)->firstOrFail();
      $resultResponse->setData($patient);
      $resultResponse->setStatusCode(ResultResponse::SUCCESS_CODE);
      $resultResponse->setMessage(ResultResponse::TXT_SUCCESS_CODE);
    } catch (\Exception $e) {
      $resultResponse->setStatusCode(ResultResponse::ERROR_ELEMENT_NOT_FOUNT_CODE);
      $resultResponse->setMessage(ResultResponse::TXT_ERROR_ELEMENT_NOT_FOUNT_CODE);
    }

    return response()->json($resultResponse);
  }


  /**
   * Retornar paciente por UserID
   * Retorna un paciente consultado por el id de usuario relacionado.  
   * 
   */
  public function showbyuser($id)
  {
    $resultResponse = new ResultResponse();
    try {
      $patient = patient::with('users')->where('user_id', $id)->firstOrFail();
      $resultResponse->setData($patient);
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
  public function edit(patient $patient)
  {
    //
  }

  /**
   * Actualizar Paciente
   * Actualiza los datos de un paciente especifico.
   */
  public function update(Request $request, $id)
  {

    $resultResponse = new ResultResponse();
    try {
      $mensaje = $this->validatePatientUpdate($request);
      if ($mensaje['estado']) {

        $patient = Patient::where('id', $id)->first();

        $patient->name = $request->get('name');
        $patient->surname = $request->get('surname');
        $patient->occupation = $request->get('occupation');
        $patient->genre = $request->get('genre');
        if ($request->hasFile('photo')) {
          $photo = $request->file('photo');
          $photoPath = $photo->store('patients_photos', 'public');
          $patient->photo = $photoPath;
        }
        $patient->phone = $request->get('phone');
        $patient->birthdate = $request->get('birthdate');
        $patient->save();

        $user = User::findOrFail($patient->user_id);
        $user->name = $request->get('name');
        $user->surname = $request->get('surname');
        $user->save();

        $resultResponse->setData($patient);
        $resultResponse->setStatusCode(ResultResponse::SUCCESS_CODE);
        $resultResponse->setMessage(ResultResponse::TXT_SUCCESS_CODE);
      } else {
        $resultResponse->setStatusCode(ResultResponse::ERROR_CODE);
        $resultResponse->setMessage($mensaje['errores']);
      }
    } catch (\Exception $e) {
      $resultResponse->setStatusCode(ResultResponse::ERROR_ELEMENT_NOT_FOUNT_CODE);
      $resultResponse->setMessage(ResultResponse::TXT_ERROR_ELEMENT_NOT_FOUNT_CODE);
    }
    return response()->json($resultResponse);
  }

  /**
   * Eliminar paciente.
   * 
   * Borrar los datos de un paciente especifico por su id.
   */
  public function destroy($id)
  {
    //
    $resultResponse = new ResultResponse();
    try {

      $patient = patient::findOrFail($id);
      $user = User::findOrFail($patient->user_id);
      $user->delete();


      $resultResponse->setData($patient);
      $resultResponse->setStatusCode(ResultResponse::SUCCESS_CODE);
      $resultResponse->setMessage(ResultResponse::TXT_SUCCESS_CODE);
    } catch (\Exception $e) {
      $resultResponse->setStatusCode(ResultResponse::ERROR_ELEMENT_NOT_FOUNT_CODE);
      $resultResponse->setMessage(ResultResponse::TXT_ERROR_ELEMENT_NOT_FOUNT_CODE);
    }

    return response()->json($resultResponse);
  }


  private function validatePatient(Request $request)
  {


    $rules = [

      'name' => 'required|string',
      'surname' => 'required|string',
      'dni' => 'required|string',
      'email' => 'required|string',
      'password' => 'required|string',
      'genre' => 'required|string',
      'phone' => 'required|string',
      'occupation' => 'required|string',

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

  private function validatePatientUpdate(Request $request)
  {


    $rules = [

      'name' => 'required|string',
      'surname' => 'required|string',
      'genre' => 'required|string',
      'occupation' => 'required|string',
      'phone' => 'required|string',

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
