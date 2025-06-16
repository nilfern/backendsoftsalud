<?php

namespace App\Http\Controllers;

use App\Models\availability;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Custom\ResultResponse;

class AvailabilityDoctorController extends Controller
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
    public function store(Request $request) //OK
    {
        $newAvailability = new Availability(
            [
                'doctor_id' => $request->get('doctor_id'),
                'date_availabilities' => $request->get('date_availabilities'),
                'start_hour' => $request->get('start_hour'),
                'end_hour' => $request->get('end_hour'),
                'status' => $request->get('status'),
            ]
        );

        $newAvailability->save();

        return response()->json($newAvailability);
    }



    /**
     * Display the specified resource.
     */
    public function show($id, $date) // OK DISPONIBILIDADES POR EL ID DEL MEDICO Y FECHA
    {
        $resultResponse = new ResultResponse();
        try {

            $availability = availability::with('doctors')->where('doctor_id', $id)->whereDate('date_availabilities', $date)->where('status', 1)->whereNotIn('id', function ($query) {
                $query->select('availabily_id')->from('appointments');
            })->get();

            if ($availability->isEmpty()) {
                $resultResponse->setData($availability);
                $resultResponse->setStatusCode(ResultResponse::ERROR_ELEMENT_NOT_FOUNT_CODE);
                $resultResponse->setMessage(ResultResponse::TXT_ERROR_ELEMENT_NOT_FOUNT_CODE);
            } else {
                $resultResponse->setData($availability);
                $resultResponse->setStatusCode(ResultResponse::SUCCESS_CODE);
                $resultResponse->setMessage(ResultResponse::TXT_SUCCESS_CODE);
            }
        } catch (\Exception $e) {
            $resultResponse->setStatusCode(ResultResponse::ERROR_ELEMENT_NOT_FOUNT_CODE);
            $resultResponse->setMessage(ResultResponse::TXT_ERROR_ELEMENT_NOT_FOUNT_CODE);
        }
      
        return response()->json($resultResponse);
    }

    public function showavailability($id, $date) //OK MUESTAR LAS DISPONIBILIDADES DEL MEDICO EN UNA FECHA
    {
        $resultResponse = new ResultResponse();
        try {

            $availability = availability::with('doctors')->where('doctor_id', $id)->whereDate('date_availabilities', $date)->whereIn('status', [1, 2])->get();

            if ($availability->isEmpty()) {
                $resultResponse->setData($availability);
                $resultResponse->setStatusCode(ResultResponse::ERROR_ELEMENT_NOT_FOUNT_CODE);
                $resultResponse->setMessage(ResultResponse::TXT_ERROR_ELEMENT_NOT_FOUNT_CODE);
            } else {
                $resultResponse->setData($availability);
                $resultResponse->setStatusCode(ResultResponse::SUCCESS_CODE);
                $resultResponse->setMessage(ResultResponse::TXT_SUCCESS_CODE);
            }
        } catch (\Exception $e) {
            $resultResponse->setStatusCode(ResultResponse::ERROR_ELEMENT_NOT_FOUNT_CODE);
            $resultResponse->setMessage(ResultResponse::TXT_ERROR_ELEMENT_NOT_FOUNT_CODE);
        }

        return response()->json($resultResponse);
    }







    public function showavailabilitySpecialty($id, $date)  //OK DISPONIBILIDADES POR EL ID DEL ESPECIALISTA  Y FECHA
    {
        $resultResponse = new ResultResponse();
        try {

            $availability = availability::with('doctors')->whereHas('doctors', function ($query) use ($id) {
                $query->where('specialty_id', $id);
            })
                ->whereDate('date_availabilities', $date)->where('status', 1)->whereNotIn('id', function ($query) {
                    $query->select('availabily_id')->from('appointments');
                })->get();



            $grouped = $availability->groupBy('doctor_id')->map(function ($availability) {
                return [
                    'doctor' => $availability->first()->doctors, // Información del doctor
                    'availabilities' => $availability->map(function ($availability) {
                        return [
                            'doctor_id' => $availability->doctor_id,
                            'id' => $availability->id,
                            'date' => $availability->date_availabilities,
                            'start_hour' => $availability->start_hour,
                            'end_hour' => $availability->end_hour,
                            'status' => $availability->status,
                        ];
                    }),
                ];
            });


          

            if ($availability->isEmpty()) {
                $resultResponse->setData($grouped->values());
                $resultResponse->setStatusCode(ResultResponse::ERROR_ELEMENT_NOT_FOUNT_CODE);
                $resultResponse->setMessage(ResultResponse::TXT_ERROR_ELEMENT_NOT_FOUNT_CODE);
            } else {
            
                $resultResponse->setData($grouped->values());
                $resultResponse->setStatusCode(ResultResponse::SUCCESS_CODE);
                $resultResponse->setMessage(ResultResponse::TXT_SUCCESS_CODE);
            }
        } catch (\Exception $e) {
            $resultResponse->setStatusCode(ResultResponse::ERROR_ELEMENT_NOT_FOUNT_CODE);
            $resultResponse->setMessage(ResultResponse::TXT_ERROR_ELEMENT_NOT_FOUNT_CODE);
        }
      
        return response()->json($resultResponse);
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(availability $availability)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, availability $availability)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) // OK ELIMINA DISPONIBILIDAD
    {

        //
        $resultResponse = new ResultResponse();
        try {

            $availability = availability::findOrFail($id);
            $availability->delete();
            $resultResponse->setData($availability);
            $resultResponse->setStatusCode(ResultResponse::SUCCESS_CODE);
            $resultResponse->setMessage(ResultResponse::TXT_SUCCESS_CODE);
        } catch (\Exception $e) {
            $resultResponse->setStatusCode(ResultResponse::ERROR_ELEMENT_NOT_FOUNT_CODE);
            $resultResponse->setMessage(ResultResponse::TXT_ERROR_ELEMENT_NOT_FOUNT_CODE);
        }

        return response()->json($resultResponse);
    }
}
