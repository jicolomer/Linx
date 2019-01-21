<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;

use Session;
use Validator;
use Auth;

use Yajra\Datatables\Datatables;

use App\Models\Empresa;
use App\Models\EmpresasDoc;
use App\Models\Trabajador;
use App\Models\TipoDocumento;
use App\Models\Documento;
use App\Models\Provincia;
use App\Models\CodigoCNAE;

class EmpresasController extends Documentos\DocumentosBaseController
{
    public function __construct()
    {
        parent::__construct(Empresa::class, 'empresas');
    }


    // EMPRESA del USUARIO logueado
    public function editEmpresa(Request $request)
    {
        return $this->edit($request, Auth::user()->empresa_id);
    }

    // public function index()

    public function create(Request $request)
    {
        $provincias = Provincia::pluck('nombre', 'id');
        $cnaes = CodigoCNAE::pluck('descripcion', 'codigo');
        $modalidades = config('enums.modalidades_preventivas');
        $actividades_construccion = config('enums.actividades_construccion');

        return parent::__create($request, compact('provincias', 'cnaes', 'modalidades', 'actividades_construccion'));
    }

    public function store(Request $request)
    {
        $construccion = $request->exists('construccion');
        $plantilla_indefinida = $request->exists('plantilla_indefinida');
        $autonomo = $request->exists('autonomo');
        $trabajadores_a_cargo = $request->exists('trabajadores_a_cargo');

        return parent::__store($request, compact('construccion', 'plantilla_indefinida', 'autonomo', 'trabajadores_a_cargo'));
    }

    public function edit(Request $request, $id)
    {
        // Empresa PRINCIPAL
        if ($id == 0) {
            $this->setReturnTo();
            $ambito = 'EMP';
        } else {
            $ambito = "CTA";
        }

        $provincias = Provincia::pluck('nombre', 'id');
        $cnaes = CodigoCNAE::pluck('descripcion', 'codigo');
        $modalidades = config('enums.modalidades_preventivas');
        $actividades_construccion = config('enums.actividades_construccion');
        $empresa_usuario = ($id == Auth::user()->empresa_id);
        $return_to = 'null';

        $trabajadores = Trabajador::where('activo', '=', true)
                                ->where('empresa_id', $id)
                                ->orderBy('apellidos')
                                ->get();

        return parent::__edit(
            $request,
            $id,
            compact(
                'ambito',
                'provincias',
                'cnaes',
                'modalidades',
                'actividades_construccion',
                'trabajadores',
                'empresa_usuario',
                $empresa_usuario ? 'return_to' : ''
            )
        );
    }

    public function update(Request $request, $id)
    {
        $construccion = $request->exists('construccion');
        $plantilla_indefinida = $request->exists('plantilla_indefinida');
        $autonomo = $request->exists('autonomo');
        $trabajadores_a_cargo = $request->exists('trabajadores_a_cargo');

        return parent::__update($request, $id, compact('construccion', 'plantilla_indefinida', 'autonomo', 'trabajadores_a_cargo'));
    }

    // public function remove($id)

    public function rowsData(Request $request)
    {
        $empresas = Empresa::select([ 'id', 'razon_social', 'cif', 'municipio', 'provincia_id', 'telefono',
                                        'modalidad_preventiva', 'codigo_cnae', 'construccion', 'autonomo', 'activo' ])
                            ->where('id', '>', 0)
                            ->where('activo', '=', true);

        // Si es el contratista y pide la lista de empresas es porque estamos en la
        // opción de menú 'Subcontratistas', por tanto filtramos para mostrarle sólo
        // sus subcontratistas
        if (Auth::user()->isExterno()) {
            $empresa_user = Empresa::findOrFail(Auth::user()->empresa_id);
            $subcontratistas = $empresa_user->subcontratistas()->pluck('id');
            $empresas = $empresas->whereIn('id', $subcontratistas);
        }

        return Datatables::of($empresas)
            ->setRowId('id')
            ->addColumn('provincia', function ($empresa) {
                Provincia::getNombre($empresa->provincia_id);
            })
            ->removeColumn('provincia_id')
            ->editColumn('modalidad_preventiva', function ($empresa) {
                return config('enums.modalidades_preventivas')[$empresa->modalidad_preventiva];
            })
            ->editColumn('construccion', function ($empresa) {
                return $this->getCheckColumn($empresa->construccion);
            })
            ->editColumn('autonomo', function ($empresa) {
                return $this->getCheckColumn($empresa->autonomo);
            })
            ->addColumn('actions', function ($empresa) {
                return $this->getActionColumn($empresa);
            })
            ->rawColumns(['construccion','autonomo','actions'])
            ->make(true);
    }

    protected function validator(array $data, $isUpdate = false)
    {
        $validator = Validator::make($data, [
            'razon_social' => 'required|max:255|' . ($isUpdate ? 'unique:empresas,razon_social,'.$data['id'] : 'unique:empresas'),
            'cif' => 'required|max:9|' . ($isUpdate ? 'unique:empresas,cif,'.$data['id'] : 'unique:empresas'),
            'codigo_postal' => 'digits:5',
            'municipio' => 'max:100',
            'telefono' => 'digits:9',
            'telefono2' => 'digits:9',
            'fax' => 'digits:9',
            'modalidad_preventiva' => 'required',
        ]);

        $fields_names = [
            'razon_social' => 'Razón Social',
            'cif' => 'CIF/DNI',
            'codigo_postal' => 'Código Postal',
            'municipio' => 'Municipio',
            'telefono' => 'Teléfono',
            'telefono2' => 'Teléfono 2',
            'fax' => 'Fax',
            'modalidad_preventiva' => 'Modalidad Preventiva',
        ];
        $validator->setAttributeNames($fields_names);

        return $validator;
    }
}
