<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;

use Auth;
use Session;
use Validator;

use Laracasts\Flash\Flash;

use App\User;


class SoporteController extends Base\BaseController
{
    use Base\EmailsTrait;

    public function __construct()
    {
        parent::__construct(User::class, 'soporte');
    }

    public function index(Request $request)
    {
        if ($email_respuesta = $request->get('email_respuesta')) {
            if ($asunto = $request->get('asunto')) {
                if ($usuario_nombre = $request->get('usuario_nombre')) {
                    // Respuesta de Soporte
                    $respuesta_soporte = true;
                    return parent::__index($request, compact('respuesta_soporte', 'usuario_nombre', 'email_respuesta', 'asunto'));
                }
            }
        }

        // Normal
        return parent::__index($request);
    }

    public function enviarMensaje(Request $request)
    {
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            return redirect()->back()
                        ->with('errors', $validator->errors())
                        ->withInput();
        }

        $respuesta_soporte = isset($request['respuesta_soporte']);
        $target = $respuesta_soporte == true ? 'usuario' : 'departamento de soporte';

        if ($email_respuesta = $request->get('email_respuesta')) {
            if ($asunto = $request->get('asunto')) {
                if ($mensaje = $request->get('mensaje')) {
                    $usuario_nombre = $request->get('usuario_nombre');
                    $this->sendSoporteEmail($respuesta_soporte, $email_respuesta, $asunto, $mensaje, $usuario_nombre);
                    Flash::success('Mensaje enviado con éxito al ' . $target . '.');
                    return redirect()->back();
                }
            }
        }

        Flash::error('¡No se ha podido enviar el mensaje al ' . $target . '!<br />Falta algún dato del formulario de envío.');
        return redirect()->back();
    }

    protected function validator(array $data)
    {
        $rules = [
            'email_respuesta' => 'required|email',
            'asunto' => 'required|max:255',
            'mensaje' => 'required',
        ];

        $validator = Validator::make($data, $rules);

        $fields_names = [
            'email_respuesta' => 'Email de Respuesta',
            'asunto' => 'Asunto',
            'mensaje' => 'Mensaje',
        ];
        $validator->setAttributeNames($fields_names);

        return $validator;
    }

}
