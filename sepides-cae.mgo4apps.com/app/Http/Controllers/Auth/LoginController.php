<?php
namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use App\Models\Empresa;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => ['logout', 'checkSession']]);
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        $empresa_principal_nombre = config('cae.nombre_corto');

        return view('auth.login', compact('empresa_principal_nombre'));
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        if(! $user->can('dashboard.view')) {
            if ($user->isControl()) {
                return redirect(route('control-acceso'));
            } else {
                redirect(route('soporte.index'));
            }
        }

        return redirect()->intended($this->redirectTo);
    }

    /**
     * Check if user session is active.
     *
     * @return Response
     */
    public function checkSession()
    {
        return response()->json(['guest' => auth()->guest()]);
    }

}
