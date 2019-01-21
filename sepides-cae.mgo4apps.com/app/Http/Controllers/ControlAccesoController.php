<?php
namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;

use Auth;
use Session;
use Validator;

use Laracasts\Flash\Flash;
use Jenssegers\Date\Date;

use App\Models\Centro;
use App\Models\Trabajador;
use App\Models\Maquina;
use App\Models\Contrato;
use App\Models\ContratoTrabajador;
use App\Models\ContratoMaquina;

class ControlAccesoController extends Base\BaseController
{
    public function __construct()
    {
        parent::__construct(Centro::class, 'control-acceso');

        if (Auth::user() && Auth::user()->centro_id) {
            $this->saveCentroId(Auth::user()->centro_id);
        }
    }

    public function index(Request $request)
    {
        $centro_nombre = '';
        $centros = Centro::where('activo', true)->pluck('nombre', 'id');
        $centro_id = $this->getCentroId();
        if ($centro_id == null) {
            $this->saveCentroId(Auth::user()->centro_id);
            $centro_id = $this->getCentroId();
        }
        if ($centro_id != null) {
            $centro = Centro::findOrfail($centro_id);
            $centro_nombre = $centro->nombre . ' (#' . $centro_id . ')';
        }
        // dd($centro_nombre);

        return parent::__index($request, compact('centros', 'centro_id', 'centro_nombre'));
    }

    public function compruebaAcceso(Request $request)
    {
        $fields = $request->only('centro_id', 'nif', 'matricula', 'new_centro_id');
        $new_centro = $request->get('new_centro_id');
        if ($new_centro != null) {
            $fields['centro_id'] = $new_centro;
        }

        $validator = $this->validator($fields);
        if ($validator->fails()) {
            return redirect()->back()
                        ->with('errors', $validator->errors())
                        ->withInput();
        }

        $centro_id = $fields['centro_id'];
        $this->saveCentroId($centro_id);
        $nif = $fields['nif'];
        $matricula = $fields['matricula'];

        if ($nif != null) {
            $trabajador = Trabajador::where('nif', '=', $nif)->first();
            if ($trabajador != null) {
                $permiso = ContratoTrabajador::where('centro_id', '=', $centro_id)
                                           ->where('trabajador_id', '=', $trabajador->id)
                                           ->whereDate('fecha_inicio_trabajos', '<=', Date::now()->toDateString())
                                           ->whereDate('fecha_fin_trabajos', '>=', Date::now()->toDateString())
                                           ->first();
                if ($permiso != null) {
                    if ($permiso->permiso_status == true) {
                        if ($this->checkPermission($permiso)) {
                            Flash::success('El trabajador <strong>' . $trabajador->nombreCompleto() . '</strong> puede acceder al Centro de Trabajo.');
                        } else {
                            Flash::warning('¡El trabajador <strong>' . $trabajador->nombreCompleto() . '</strong> no tiene permiso para acceder hoy al Centro de Trabajo!');
                        }
                    } else {
                        Flash::warning('¡El trabajador <strong>' . $trabajador->nombreCompleto() . '</strong> no tiene permiso de acceso al Centro de Trabajo!');
                    }
                }
            } else {
                Flash::warning('¡No existe un trabajador con ese NIF!');
            }

        } else if ($matricula != null) {
            $maquina = Maquina::where('matricula', '=', $matricula)->first();
            if ($maquina != null) {
                $permiso = ContratoMaquina::where('centro_id', '=', $centro_id)
                                           ->where('maquina_id', '=', $maquina->id)
                                           ->whereDate('fecha_inicio_trabajos', '<=', Date::now()->toDateString())
                                           ->whereDate('fecha_fin_trabajos', '>=', Date::now()->toDateString())
                                           ->first();
                if ($permiso != null) {
                    if ($permiso->permiso_status == true) {
                        if ($this->checkPermission($permiso)) {
                            Flash::success('La máquina <strong>' . $maquina->nombre . '</strong> puede acceder al Centro de Trabajo.');
                        } else {
                            Flash::warning('¡La máquina <strong>' . $maquina->nombre . '</strong> no tiene permiso para acceder hoy al Centro de Trabajo!');
                        }
                    } else {
                        Flash::warning('¡La máquina <strong>' . $maquina->nombre . '</strong> no tiene permiso de acceso al Centro de Trabajo!');
                    }
                }
            } else {
                Flash::warning('¡No existe una máquina con esa matrícula!');
            }

        }

        return redirect(route('control-acceso'));
    }

    private function checkPermission($permiso)
    {
        // Comprobamos si puede trabajar este día de la semana
        $puede = true;
        $diaDeLaSemana = idate('w');
        switch ($diaDeLaSemana) {
            case 1:
                $puede = $permiso->trabaja_lunes;
                break;
            case 2:
                $puede = $permiso->trabaja_martes;
                break;
            case 3:
                $puede = $permiso->trabaja_miercoles;
                break;
            case 4:
                $puede = $permiso->trabaja_jueves;
                break;
            case 5:
                $puede = $permiso->trabaja_viernes;
                break;
            case 6:
                $puede = $permiso->trabaja_sabado;
                break;
            case 7:
                $puede = $permiso->trabaja_domingo;
                break;
        }

        return $puede;
    }

    protected function validator(array $data)
    {
        $rules = [
            'centro_id' => 'required|min:1',
            'nif' => 'required_without:matricula|min:9|max:9',
            'matricula' => 'required_without:nif|max:20',
        ];

        $validator = Validator::make($data, $rules);

        $fields_names = [
            'centro_id' => 'Centro de Trabajo',
            'nif' => 'NIF/DNI',
            'matricula' => 'Matrícula',
        ];
        $validator->setAttributeNames($fields_names);

        return $validator;
    }

    private function saveCentroId($centro_id)
    {
        Session::put('control_accesos_centro_id', $centro_id);
    }

    private function getCentroId()
    {
        if ($centro_id = Session::get('control_accesos_centro_id')) {
            return $centro_id;
        }
        return null;
    }

}
