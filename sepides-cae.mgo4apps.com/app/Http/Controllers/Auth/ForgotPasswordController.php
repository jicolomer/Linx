<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

use App\Models\Empresa;


class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails, \App\Http\Controllers\Base\EmailsTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLinkRequestForm()
    {
        $empresa_principal_nombre = config('cae.nombre_corto');

        return view('auth.passwords.email', compact('empresa_principal_nombre'));
    }


    // Función para enviar el email para el reset de la contraseña
    public function sendResetLinkEmail(Request $request)
    {
        $this->validate($request, ['email' => 'required|email']);
        // $this->validateSendResetLinkEmail($request);

        // $broker = $this->getBroker();

        // $response = $this->sendMail(Password::broker($broker), $this->getSendResetLinkEmailCredentials($request));
        $response = $this->sendMail($this->broker(), $request->only('email'));

        if ($response === Password::RESET_LINK_SENT) {
            return back()->with('status', trans($response));
        }

        // If an error was returned by the password broker, we will get this message
        // translated so we can notify a user of the problem. We'll redirect back
        // to where the users came from so they can attempt this process again.
        return back()->withErrors(
            ['email' => trans($response)]
        );
    }

    private function sendMail($broker, $credentials)
    {
        $user = $broker->getUser($credentials);

        if (is_null($user)) {
            return Password::INVALID_USER;
        }

        // El token para el reset
        $token = $broker->createToken($user);

        $this->sendResetUserPasswordEmail($user, $token);

        return Password::RESET_LINK_SENT;
    }

}
