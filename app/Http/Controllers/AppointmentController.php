<?php

namespace App\Http\Controllers;

use App\Models\appointment;
use App\Models\availability;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Custom\ResultResponse;
use Illuminate\Support\Carbon;

class AppointmentController extends Controller
{

    public function index() {}


    public function create() {}

    /**
     * Registrar datos de la cita.
     * 
     * En este end point se registran los datos de la cita.
     * 
     */
    public function store(Request $request)
    {

        $resultResponse = new ResultResponse();
        try {

            $mensaje = $this->validateAppointment($request);

            if ($mensaje['estado']) {

                $newAppointment = new appointment([
                    'date_appointments' => $request->get('date_appointments'),
                    'start_hour' => $request->get('start_hour'),
                    'doctor_id' => $request->get('doctor_id'),
                    'patient_id' => $request->get('patient_id'),
                    'availabily_id' => $request->get('availabily_id'),
                    'status' => "pendiente",

                ]);
                $newAppointment->save();

                $availability = availability::findOrFail($request->get('availabily_id'));
                $availability->status = 2;
                $availability->save();

                $resultResponse->setData($newAppointment);
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
     * Display the specified resource.
     */
    public function show(appointment $appointment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(appointment $appointment)
    {
        //
    }

    /**
     * 
     */
    public function update(Request $request, appointment $appointment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(appointment $appointment)
    {
        //
    }


    private function validateAppointment(Request $request)
    {


        $rules = [

            'date_appointments' => 'required|date',
            'start_hour' => 'required|string',
            'doctor_id' => 'required|integer',
            'patient_id' => 'required|integer'
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


    /**
     * Retorna las citas del paciente en una fecha seleccionada
     *
     */

    public function appointmentbypatient($id, $date)
    {

        $resultResponse = new ResultResponse();
        try {
            $appointment = Appointment::with(['doctors', 'availabilities', 'patients'])->where('patient_id', $id)->whereMonth('date_appointments', date('m', strtotime($date)))
                ->whereYear('date_appointments', date('Y', strtotime($date)))->whereHas('availabilities', function ($query) {
                    $query->where('status', '!=', 0);
                })
                ->get();

            if ($appointment->isEmpty()) {
                $resultResponse->setData($appointment);
                $resultResponse->setStatusCode(ResultResponse::ERROR_ELEMENT_NOT_FOUNT_CODE);
                $resultResponse->setMessage(ResultResponse::TXT_ERROR_ELEMENT_NOT_FOUNT_CODE);
            } else {
                $resultResponse->setData($appointment);
                $resultResponse->setStatusCode(ResultResponse::SUCCESS_CODE);
                $resultResponse->setMessage(ResultResponse::TXT_SUCCESS_CODE);
            }
        } catch (\Exception $e) {
            $resultResponse->setStatusCode(ResultResponse::ERROR_ELEMENT_NOT_FOUNT_CODE);
            $resultResponse->setMessage(ResultResponse::TXT_ERROR_ELEMENT_NOT_FOUNT_CODE + ":" + $e);
        }

        return response()->json($resultResponse);
    }


    /**
     * Retorna las citas del médico en una fecha seleccionada
     *
     */

    public function appointmentbydoctor($id, $date)
    {

        $resultResponse = new ResultResponse();
        try {
            $appointment = appointment::with(['doctors', 'availabilities', 'patients'])->where('doctor_id', $id)->whereDate('date_appointments', $date)->whereHas('availabilities', function ($query) {
                $query->where('status', '!=', 0);
            })->get();

            if ($appointment->isEmpty()) {
                $resultResponse->setData($appointment);
                $resultResponse->setStatusCode(ResultResponse::ERROR_ELEMENT_NOT_FOUNT_CODE);
                $resultResponse->setMessage(ResultResponse::TXT_ERROR_ELEMENT_NOT_FOUNT_CODE);
            } else {
                $resultResponse->setData($appointment);
                $resultResponse->setStatusCode(ResultResponse::SUCCESS_CODE);
                $resultResponse->setMessage(ResultResponse::TXT_SUCCESS_CODE);
            }
        } catch (\Exception $e) {
            $resultResponse->setStatusCode(ResultResponse::ERROR_ELEMENT_NOT_FOUNT_CODE);
            $resultResponse->setMessage(ResultResponse::TXT_ERROR_ELEMENT_NOT_FOUNT_CODE + ":" + $e);
        }

        return response()->json($resultResponse);
    }

    /**
     * Retorna todas las citas de una fecha seleccionada.
     */
    public function appointmentall($date)
    {

        $resultResponse = new ResultResponse();
        try {

            $appointment = appointment::with(['doctors', 'availabilities', 'patients'])->whereDate('date_appointments', $date)->whereHas('availabilities', function ($query) {
                $query->where('status', '!=', 0);
            })->get();

            if ($appointment->isEmpty()) {
                $resultResponse->setData($appointment);
                $resultResponse->setStatusCode(ResultResponse::ERROR_ELEMENT_NOT_FOUNT_CODE);
                $resultResponse->setMessage(ResultResponse::TXT_ERROR_ELEMENT_NOT_FOUNT_CODE);
            } else {
                $resultResponse->setData($appointment);
                $resultResponse->setStatusCode(ResultResponse::SUCCESS_CODE);
                $resultResponse->setMessage(ResultResponse::TXT_SUCCESS_CODE);
            }
        } catch (\Exception $e) {
            $resultResponse->setStatusCode(ResultResponse::ERROR_ELEMENT_NOT_FOUNT_CODE);
            $resultResponse->setMessage(ResultResponse::TXT_ERROR_ELEMENT_NOT_FOUNT_CODE + ":" + $e);
        }

        return response()->json($resultResponse);
    }

    /**
     * Actualiza una cita especifica como atendida.
     */
    public function attendappointment($id)
    {
        $appointment = appointment::where('id', $id)->first();
        $appointment->status = "attend";
        $appointment->save();
        return response()->json(['Status' => 'attend'], 200);
    }

    /**
     * Elimina una cita especifica.
     */
    public function cancelappointment($id)
    {
        $appointment = appointment::where('id', $id)->first();
        $availability = availability::findOrFail($appointment->availabily_id);
        $availability->status = 1;
        $availability->save();
        $appointment->delete();
        return response()->json(['Status' => 'Cancel'], 200);
    }

    /**
     * Retorna la cantidad de citas del día, la cantidad de citas pendiente del día, la cantidad de citas del mes, la cantidad de citas pendiente del mes del médico el día de atención.
     */
    public function appointmentbydoctorcount($id, $date)
    {

        $resultResponse = new ResultResponse();
        try {
            $parsedDate = Carbon::parse($date);
            $appointmentcount = appointment::where('doctor_id', $id)->whereDate('date_appointments', $date)->whereHas('availabilities', function ($query) {
                $query->where('status', '!=', 0);
            })->count();
            $appointmentcountPending = appointment::where('doctor_id', $id)->where('status', 'pendiente')->whereDate('date_appointments', $date)->whereHas('availabilities', function ($query) {
                $query->where('status', '!=', 0);
            })->count();
            $appointmentCountMonth = Appointment::where('doctor_id', $id)->whereYear('date_appointments', $parsedDate->year)->whereMonth('date_appointments', $parsedDate->month)->whereHas('availabilities', function ($query) {
                $query->where('status', '!=', 0);
            })->count();
            $appointmentCountMonthPending = Appointment::where('doctor_id', $id)->where('status', 'pendiente')->whereYear('date_appointments', $parsedDate->year)->whereMonth('date_appointments', $parsedDate->month)->whereHas('availabilities', function ($query) {
                $query->where('status', '!=', 0);
            })->count();

            $resultResponse->setData($appointmentcount);
            $resultResponse->setStatusCode(ResultResponse::SUCCESS_CODE);
            $resultResponse->setMessage(ResultResponse::TXT_SUCCESS_CODE);
        } catch (\Exception $e) {
            $resultResponse->setStatusCode(ResultResponse::ERROR_ELEMENT_NOT_FOUNT_CODE);
            $resultResponse->setMessage(ResultResponse::TXT_ERROR_ELEMENT_NOT_FOUNT_CODE + ":" + $e);
        }
        return response()->json(['appointmentday' => $appointmentcount, 'appointmentdaypending' => $appointmentcountPending, 'appointmentmonth' => $appointmentCountMonth, 'appointmentmonthpending' => $appointmentCountMonthPending], 200);
    }


    /**
     * Retorna la cantidad de citas del día, la cantidad de citas pendiente del día, la cantidad de citas del mes, la cantidad de citas pendiente del mes del día de atención
     */
    public function appointmentallcount($date)
    {

        $resultResponse = new ResultResponse();
        try {

            $parsedDate = Carbon::parse($date);
            $appointmentcount = appointment::whereDate('date_appointments', $date)->whereHas('availabilities', function ($query) {
                $query->where('status', '!=', 0);
            })->count();
            $appointmentcountPending = appointment::where('status', 'pendiente')->whereDate('date_appointments', $date)->whereHas('availabilities', function ($query) {
                $query->where('status', '!=', 0);
            })->count();
            $appointmentCountMonth = Appointment::whereYear('date_appointments', $parsedDate->year)->whereMonth('date_appointments', $parsedDate->month)->whereHas('availabilities', function ($query) {
                $query->where('status', '!=', 0);
            })->count();
            $appointmentCountMonthPending = Appointment::where('status', 'pendiente')->whereYear('date_appointments', $parsedDate->year)->whereMonth('date_appointments', $parsedDate->month)->whereHas('availabilities', function ($query) {
                $query->where('status', '!=', 0);
            })->count();

            $resultResponse->setData($appointmentcount);
            $resultResponse->setStatusCode(ResultResponse::SUCCESS_CODE);
            $resultResponse->setMessage(ResultResponse::TXT_SUCCESS_CODE);
        } catch (\Exception $e) {
            $resultResponse->setStatusCode(ResultResponse::ERROR_ELEMENT_NOT_FOUNT_CODE);
            $resultResponse->setMessage(ResultResponse::TXT_ERROR_ELEMENT_NOT_FOUNT_CODE + ":" + $e);
        }
        return response()->json(['appointmentday' => $appointmentcount, 'appointmentdaypending' => $appointmentcountPending, 'appointmentmonth' => $appointmentCountMonth, 'appointmentmonthpending' => $appointmentCountMonthPending], 200);
    }
}
