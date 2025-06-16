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
   * Display a listing of the resource.
   */
  public function index()
  {
    //
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
              $photoPath = $photo->store('doctors_photos', 'public');
            } else {
              $photoPath = "doctors_photos/usuario.jpg";
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
   * Display the specified resource.
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
   * Show the form for editing the specified resource.
   */
  public function edit(patient $patient)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, patient $patient)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(patient $patient)
  {
    //
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
}
