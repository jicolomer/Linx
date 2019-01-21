<?php
namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;

use Auth;
use DB;
use File;

use Yajra\Datatables\Datatables;
use Jenssegers\Date\Date;

use App\Models\Contrato;
use App\Models\Empresa;
use App\Models\Trabajador;
use App\Models\Maquina;
use App\Models\Aviso;

class DashboardController extends Base\BaseController
{
    public function __construct()
    {
        parent::__construct(Contrato::class, 'dashboard');
    }

    public function index(Request $request)
    {
        // Si no tiene permiso para entrar aquí. Pongo esto porque la ruta '/'
        // no se puede proteger con el middleware porque da error cuando el usuario
        // no está logueado
        if (! Auth::user()->can('dashboard.view')) {
            if (Auth::user()->isControl()) {
                return redirect(route('control-acceso'));
            } else {
                return abort(401, 'No tiene permiso para acceder.');
            }
        }

        if (Auth::user()->isExterno()) {
            $empresa_id = Auth::user()->empresa_id;
            // Nº Contratos ACTIVOS donde aparece el contratista como tal o como subcontratista
            $num_contratos = Empresa::findOrFail($empresa_id)->contratos()->where('activo', '=', true)->count();
            // Nº de subcontratistas de la empresa
            $num_empresas = DB::table('contratos_contratistas')->where('empresa_id', $empresa_id)->where('subcontratista_id', '>', 0)->count();
            $num_trabajadores = Trabajador::where('activo', true)->where('empresa_id', $empresa_id)->count();
            $num_maquinas = Maquina::where('activo', true)->where('empresa_id', $empresa_id)->count();
        } else {
            $num_contratos = Contrato::where('activo', '=', true)->count();
            $num_empresas = Empresa::where('activo', true)->where('id', '>', 0)->count();
            $num_trabajadores = Trabajador::where('activo', true)->where('empresa_id', '>', 0)->count();
            $num_maquinas = Maquina::where('activo', true)->count();
        }

        $changelog = $this->getChangelogFile();

        return parent::__index($request, compact('num_contratos', 'num_empresas', 'num_trabajadores', 'num_maquinas', 'changelog'));
    }

    private function getChangelogFile()
    {
        try {
            $filename = base_path().'/resources/data/changelog.html';
            $contents = File::get($filename);
            return $contents;
        } catch (Illuminate\Filesystem\FileNotFoundException $exception) {
            return "No hay ninguna actualización.";
        }
    }




    // *************************************************************************
    //  AVISOS
    // *************************************************************************

    // Datos para el datatable de avisos
    public function avisosData(Request $request)
    {
        $avisos = Auth::user()
                        ->avisos()
                        ->orderBy('created_at', 'desc')
                        ->limit(50)
                        ->select('id', 'texto', 'leido', 'created_at');

        $datatable = Datatables::of($avisos)
            ->addColumn('leido_icon', function ($aviso) {
                if ($aviso->leido == false) {
                    return '<span class="text-light-blue"><i class="fa fa-circle"></i></span>';
                } else {
                    return ' ';
                }
            })
            ->addColumn('fecha', function ($aviso) {
                $date = new Date($aviso->created_at);
                return $date->ago();
            })
            ->rawColumns(['leido_icon','texto']);

        return $datatable->make(true);
    }


public function el(string $tag, $attributes = null, $content = null) : string
{
    return \Spatie\HtmlElement\HtmlElement::render(...func_get_args());
}

    public function avisosGo($id)
    {
        $aviso = Aviso::findOrFail($id);

        // Lo ponemos como leído
        Auth::user()->avisos()->updateExistingPivot($id, ['leido' => true]);

        return redirect($aviso->url);
    }
}
