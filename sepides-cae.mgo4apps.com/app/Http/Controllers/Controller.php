<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        $this->middleware('auth');
    }


    // Funciones para devolver una respuesta AJAX (JSON) al front end
    protected function returnSuccess($msg)
    {
        return $this->returnJson('success', $msg);
    }

    protected function returnError($msg)
    {
        return $this->returnJson('error', $msg);
    }

    protected function returnJson($result, $msg)
    {
        return [
            'result' => $result,
            'msg' => $msg
        ];
    }
}
