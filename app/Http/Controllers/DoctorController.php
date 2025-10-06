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
   * Lista de medicos
   * Retorna la lista de médicos registrados.
   * 
   * @response array{
   *   current_page: 1,
   *   data: array{
   *     id: int,
   *     name: string,
   *     surname: string,
   *     email: string,
   *     users: array{
   *       id: int,
   *       email: string,
   *       role: string
   *     }
   *   }[],
   *   first_page_url: "http://127.0.0.1:8000/api/employee?page=1",
   *   from: 1,
   *   last_page: 2,
   *   last_page_url: "http://127.0.0.1:8000/api/employee?page=2",
   *   links: array{
   *     url: ?string,
   *     label: string,
   *     active: bool
   *   }[],
   *   next_page_url: "http://127.0.0.1:8000/api/employee?page=2",
   *   path: "http://127.0.0.1:8000/api/employee",
   *   per_page: 10,
   *   prev_page_url: ?string,
   *   to: 10,
   *   total: 11
   * }
   */
  public function index()
  {
    $doctor = doctor::with('users')->paginate(10);
    $resultResponse = new ResultResponse();
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
   * Registrar medico
   * Registra los datos del médico.
   */
  public function store(Request $request)
  {
    $resultResponse = new ResultResponse();
    try {
      $mensaje = $this->validateDoctor($request);

      if ($mensaje['estado']) {

        $existe = doctor::where('dni', $request->get('dni'))->exists();
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

            $newDoctor = new doctor([
              'name' => $request->get('name'),
              'surname' => $request->get('surname'),
              'dni' => $request->get('dni'),
              'genre' => $request->get('genre'),
              'photo' => $photoPath,
              'phone' => $request->get('phone'),
              'email' => $request->get('email'),
              'password' => bcrypt($request->get('password')),
              'birthdate' => $request->get('birthdate'),
              'address' => $request->get('address'),
              'user_id' => $newUser->id,
              'specialty_id' => $request->get('specialty'),
            ]);
            $request->get('photo');
            $newDoctor->save();
            $resultResponse->setData($newDoctor);
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
   * Retorna medico por DNI
   * Retorna un médico consultado por su dni.
   */
  public function show($id)
  {
    $resultResponse = new ResultResponse();
    try {
      $doctor = Doctor::with('users')->where('dni', $id)->firstOrFail();
      $resultResponse->setData($doctor);
      $resultResponse->setStatusCode(ResultResponse::SUCCESS_CODE);
      $resultResponse->setMessage(ResultResponse::TXT_SUCCESS_CODE);
    } catch (\Exception $e) {
      $resultResponse->setStatusCode(ResultResponse::ERROR_ELEMENT_NOT_FOUNT_CODE);
      $resultResponse->setMessage(ResultResponse::TXT_ERROR_ELEMENT_NOT_FOUNT_CODE);
    }

    return response()->json($resultResponse);
  }

  /**
   * Retornar medico por UserID
   * Retorna un medico consultado por el id de usuario relacionado.  
   * 
   */
  public function showbyuser($id)
  {
    $resultResponse = new ResultResponse();
    try {


      $doctor = Doctor::with('users')->where('user_id', $id)->firstOrFail();
      $resultResponse->setData($doctor);
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
  public function edit(doctor $doctor)
  {
    //
  }

  /**
   * Actualiza medico
   * Actualiza los datos de un medico especifico.
   */
  public function update(Request $request, $id)
  {
    $resultResponse = new ResultResponse();
    try {
      $mensaje = $this->validateDoctorUpdate($request);
      if ($mensaje['estado']) {

        $doctor = Doctor::where('id', $id)->first();

        $doctor->name = $request->get('name');
        $doctor->surname = $request->get('surname');

        $doctor->genre = $request->get('genre');
        if ($request->hasFile('photo')) {
          $photo = $request->file('photo');
          $photoPath = $photo->store('doctors_photos', 'public');
          $doctor->photo = $photoPath;
        }
        $doctor->phone = $request->get('phone');
        $doctor->birthdate = $request->get('birthdate');
        $doctor->address = $request->get('address');
        $doctor->specialty_id = $request->get('specialty');

        $doctor->save();

        $user = User::findOrFail($doctor->user_id);
        $user->name = $request->get('name');
        $user->surname = $request->get('surname');
        $user->save();

        $resultResponse->setData($doctor);
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
   * Eliminar Medico
   * Borrar los datos de un médico especifico por su id.
   */
  public function destroy($id)
  {
    //
    $resultResponse = new ResultResponse();
    try {

      $doctor = doctor::findOrFail($id);
      $user = User::findOrFail($doctor->user_id);
      $user->delete();


      $resultResponse->setData($doctor);
      $resultResponse->setStatusCode(ResultResponse::SUCCESS_CODE);
      $resultResponse->setMessage(ResultResponse::TXT_SUCCESS_CODE);
    } catch (\Exception $e) {
      $resultResponse->setStatusCode(ResultResponse::ERROR_ELEMENT_NOT_FOUNT_CODE);
      $resultResponse->setMessage(ResultResponse::TXT_ERROR_ELEMENT_NOT_FOUNT_CODE);
    }

    return response()->json($resultResponse);
  }

  /**
   * Retornar medico por ID
   * Retorna un medico consultado por su id.
   *  
   */
  public function showbyid($id)
  {

    $resultResponse = new ResultResponse();
    try {
      $doctor = doctor::with('users')->where('id', $id)->firstOrFail();
      $resultResponse->setData($doctor);
      $resultResponse->setStatusCode(ResultResponse::SUCCESS_CODE);
      $resultResponse->setMessage(ResultResponse::TXT_SUCCESS_CODE);
    } catch (\Exception $e) {
      $resultResponse->setStatusCode(ResultResponse::ERROR_ELEMENT_NOT_FOUNT_CODE);
      $resultResponse->setMessage(ResultResponse::TXT_ERROR_ELEMENT_NOT_FOUNT_CODE);
    }

    return response()->json($resultResponse);
  }

  private function validateDoctor(Request $request)
  {


    $rules = [
      'name' => 'required|string',
      'surname' => 'required|string',
      'dni' => 'required|string',
      'email' => 'required|string',
      'password' => 'required|string',
      'genre' => 'required|string',
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


  private function validateDoctorUpdate(Request $request)
  {


    $rules = [

      'name' => 'required|string',
      'surname' => 'required|string',
      'genre' => 'required|string',
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
