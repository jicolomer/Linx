<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;

use Validator;
use DB;

use Yajra\Datatables\Datatables;
use Jenssegers\Date\Date;

use App\Models\Maquina;
use App\Models\TipoMaquina;
use App\Models\TipoDocumento;
use App\Models\Empresa;

class MaquinasController extends Documentos\DocumentosBaseController
{
    public function __construct()
    {
        parent::__construct(Maquina::class, 'maquinas', 'máquina');
    }

    // public function index()

    public function create(Request $request)
    {
        $empresa_id = parent::getEmpresaIdFromRequest($request);
        $tipos_maquinas = TipoMaquina::pluck('nombre', 'id');

        $empresas = null;
        $empresa_nombre = null;
        if ($empresa_id === 0 || $empresa_id > 0) {
            $empresa_nombre = Empresa::getNombreEmpresa($empresa_id);
        } else {
            $empresas = Empresa::getExternasList();
        }

        return parent::__create($request, compact('tipos_maquinas', 'empresas', 'empresa_id', 'empresa_nombre'));
    }

    // public function store(Request $request)

    public function edit(Request $request, $id)
    {
        $tipos_maquinas = TipoMaquina::pluck('nombre', 'id');

        $maquina = Maquina::find($id);
        $empresa_nombre = Empresa::getNombreEmpresa($maquina->empresa_id);
        $ambito = 'MAQ';

        return parent::__edit($request, $id, compact('tipos_maquinas', 'empresa_nombre', 'ambito'));
    }

    // public function update(Request $request, $id)

    // public function remove()

    public function rowsData(Request $request)
    {
        $archive = request()->get('h');

        $maquinas = Maquina::join('tipos_maquinas', 'maquinas.tipo_maquina_id', '=', 'tipos_maquinas.id')
                        ->join('empresas', 'maquinas.empresa_id', '=', 'empresas.id')
                        ->select([ 'maquinas.*', DB::raw('tipos_maquinas.nombre as tipo'),
                                    DB::raw("CONCAT(empresas.razon_social, ', (#', empresas.id, ')') AS empresa")
                        ])
                        ->where('maquinas.activo', '=', !$archive);

        if (isset($request['e'])) {
            $empresa_id = $request->get('e');
            $maquinas->where('maquinas.empresa_id', '=', $empresa_id);
        } else {
            $maquinas->where('maquinas.empresa_id', '>', 0);
        }

        $datatable = Datatables::of($maquinas)
            ->setRowId('id')
            ->removeColumn('created_at')
            ->removeColumn('updated_at')
            ->addColumn('documentacion', function ($maquina) {
                return $this->getDocStatusColumn($maquina->statusDocumentacion());
            })
            ->rawColumns(['documentacion']);

        return $datatable->make(true);
    }

    protected function validator(array $data, $isUpdate = false)
    {
        $validator = Validator::make($data, [
            'empresa_id' => 'required',
            'tipo_maquina_id' => 'required|min:1',
            'nombre' => 'required|max:100',
            'matricula' => 'required|max:20|' . ($isUpdate ? 'unique:maquinas,matricula,'.$data['id'] : 'unique:maquinas'),
            'num_serie' => 'max:50',
            'num_bastidor' => 'max:50',
            'anio_fabricacion' => 'required|digits:4|integer|min:1900|max:' . Date::now()->format('Y'),
            'fecha_alta' => 'required|date_format:d/m/Y'
        ]);

        $fields_names = [
            'empresa_id' => 'Empresa',
            'tipo_maquina_id' => 'Tipo de Máquina',
            'nombre' => 'Nombre',
            'matricula' => 'Matrícula',
            'num_serie' => 'Número de serie',
            'num_bastidor' => 'Número de bastidor',
            'anio_fabricacion' => 'Año de fabricación',
            'fecha_alta' => 'Fecha Alta',
        ];
        $validator->setAttributeNames($fields_names);

        return $validator;
    }

    public function docFaltanteData(Request $request)
    {
        $maquina = Maquina::find($this->getEditingId());
        $tipos_documentos = $maquina->tiposDocumentosFaltantes()->get();

        $datatable = Datatables::of($tipos_documentos)
            ->editColumn('ambito', function ($tipo) {
                return config('enums.doc_scopes')[$tipo->ambito];
            })
            ->editColumn('tipo_caducidad', function ($tipo) {
                return config('enums.tipos_caducidad')[$tipo->tipo_caducidad];
            })
            ->addColumn('tags', function ($documento) {
                return $this->getTagsColumn($documento);
            })
            ->addColumn('obligatorio', function ($tipo_documento) {
                return $this->getCheckColumn($tipo_documento->pivot->obligatorio == 1);
            })
            ->addColumn('actions', function ($tipo_documento) {
                return '<button type="button" class="btn btn-danger bootstrap-modal-form-open" data-toggle="modal" data-target="#documentos-modal-dialog" data-new="true" data-tipo-doc="' . $tipo_documento->id .
                            '"><i class="fa fa-plus"></i> Añadir</button>';
            })
            ->rawColumns(['tags','obligatorio','actions']);

        return $datatable->make(true);
    }
}
