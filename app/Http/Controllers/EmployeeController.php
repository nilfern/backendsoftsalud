<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\employee;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Custom\ResultResponse;




class EmployeeController extends Controller
{
  public $url;


  /**
   * Display a listing of the resource.
   */
  public function index()
  {

    $employee = employee::with('users')->paginate(10);
    $resultResponse = new ResultResponse();
    $resultResponse->setData($employee);
    $resultResponse->setStatusCode(ResultResponse::SUCCESS_CODE);
    $resultResponse->setMessage(ResultResponse::TXT_SUCCESS_CODE);
    //return response()->json($resultResponse);
    return response()->json($employee);
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

      $mensaje = $this->validateEmployee($request);

      if ($mensaje['estado']) {

        $existe = employee::where('dni', $request->get('dni'))->exists();
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
              $photoPath = $photo->store('employees_photos', 'public');
            } else {
              $photoPath = "employees_photos/usuario.jpg";
            }
            $newEmployee = new employee([
              'name' => $request->get('name'),
              'surname' => $request->get('surname'),
              'dni' => $request->get('dni'),
              'occupation' => $request->get('occupation'),
              'gross_salary' => $request->get('gross_salary'),
              'email' => $request->get('email'),
              'password' => bcrypt($request->get('password')),
              'genre' => $request->get('genre'),
              'photo' => $photoPath,
              'phone' => $request->get('phone'),
              'birthdate' => $request->get('birthdate'),
              'address' => $request->get('address'),
              'user_id' => $newUser->id,
            ]);

            $newEmployee->save();
            $resultResponse->setData($newEmployee);
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
      $employee = Employee::with('users')->where('dni', $id)->firstOrFail();
      $resultResponse->setData($employee);
      $resultResponse->setStatusCode(ResultResponse::SUCCESS_CODE);
      $resultResponse->setMessage(ResultResponse::TXT_SUCCESS_CODE);
    } catch (\Exception $e) {
      $resultResponse->setStatusCode(ResultResponse::ERROR_ELEMENT_NOT_FOUNT_CODE);
      $resultResponse->setMessage(ResultResponse::TXT_ERROR_ELEMENT_NOT_FOUNT_CODE);
    }

    return response()->json($resultResponse);
  }


  public function showbyid($id)
  {

    $resultResponse = new ResultResponse();
    try {
      $employee = Employee::with('users')->where('id', $id)->firstOrFail();
      $resultResponse->setData($employee);
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
   * 
   * 
   * 
   * 
   * 
   */


  public function showbyuser($id)
  {
    $resultResponse = new ResultResponse();
    try {
      $employee = Employee::with('users')->where('user_id', $id)->firstOrFail();
      $resultResponse->setData($employee);
      $resultResponse->setStatusCode(ResultResponse::SUCCESS_CODE);
      $resultResponse->setMessage(ResultResponse::TXT_SUCCESS_CODE);
    } catch (\Exception $e) {
      $resultResponse->setStatusCode(ResultResponse::ERROR_ELEMENT_NOT_FOUNT_CODE);
      $resultResponse->setMessage(ResultResponse::TXT_ERROR_ELEMENT_NOT_FOUNT_CODE);
    }
    return response()->json($resultResponse);
  }


  public function edit(employee $employee)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   */

  public function update(Request $request, $id)
  {

    $resultResponse = new ResultResponse();
    try {
      $mensaje = $this->validateEmployeeUpdate($request);
      if ($mensaje['estado']) {

        $employee = Employee::where('id', $id)->first();

        $employee->name = $request->get('name');
        $employee->surname = $request->get('surname');
        $employee->occupation = $request->get('occupation');
        $employee->gross_salary = $request->get('gross_salary');
        $employee->genre = $request->get('genre');
        if ($request->hasFile('photo')) {
          $photo = $request->file('photo');
          $photoPath = $photo->store('employees_photos', 'public');
          $employee->photo = $photoPath;
        }
        $employee->phone = $request->get('phone');
        $employee->birthdate = $request->get('birthdate');
        $employee->save();

        $user = User::findOrFail($employee->user_id);
        $user->name = $request->get('name');
        $user->surname = $request->get('surname');
        $user->save();

        $resultResponse->setData($employee);
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
   * Remove the specified resource from storage.
   */
  public function destroy(employee $employee)
  {
    //
  }


  private function validateEmployee(Request $request)
  {


    $rules = [

      'name' => 'required|string',
      'surname' => 'required|string',
      'dni' => 'required|string',
      'genre' => 'required|string',
      'occupation' => 'required|string',
      'gross_salary' => 'required',
      'email' => 'required|string',
      'password' => 'required|string',
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



  private function validateEmployeeUpdate(Request $request)
  {


    $rules = [

      'name' => 'required|string',
      'surname' => 'required|string',
      'dni' => 'required|string',
      'genre' => 'required|string',
      'occupation' => 'required|string',
      'gross_salary' => 'required',
      'email' => 'required|string',     
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
