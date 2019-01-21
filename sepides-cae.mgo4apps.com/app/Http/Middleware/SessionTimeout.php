<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Illuminate\Session\Store;

class SessionTimeout
{

    // protected $session;
    protected $timeout;

    public function __construct(Store $session)
    {
        // $this->session = $session;
        $this->timeout = config('session.lifetime', 30) * 60;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (! $request->is("login")) {

            if (! session()->has('lastActivityTime')) {

                session()->put('lastActivityTime', time());

            } elseif ((time() - session()->get('lastActivityTime')) > $this->getTimeOut()) {

                if ($request->ajax() || $request->wantsJson()) {
                    return response('Unauthorized.', 401);
                } else {
                    session()->forget('lastActivityTime');
                    Auth::logout();
                    return redirect()->guest('login')->withErrors(['Su sesión ha expirado después de ' . ($this->timeout / 60) . ' minutos de inactividad.<br>Por favor, inicie sesión de nuevo.']);
                }

            }

            if (! $request->is("check-session")) {
                session()->put('lastActivityTime', time());
            }
        }

        return $next($request);
    }

    protected function getTimeOut()
    {
        return (env('TIMEOUT')) ?: $this->timeout;
    }
}
