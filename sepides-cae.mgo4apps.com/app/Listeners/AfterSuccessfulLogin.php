<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Auth;
use Session;

use App\Models\Empresa;


class AfterSuccessfulLogin
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        // Metemos en la sesión algunos datos del usuario para usarlo en vistas
        $nombre = Empresa::getNombreEmpresa(Auth::user()->empresa_id);
        Session::put('user_empresa_display_name', $nombre);

    }
}
