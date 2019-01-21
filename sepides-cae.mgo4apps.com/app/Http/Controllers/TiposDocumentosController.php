<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;

use Validator;

use Yajra\Datatables\Datatables;

use App\Models\TipoDocumento;

class TiposDocumentosController extends Base\BaseController
{
    public function __construct()
    {
        parent::__construct(TipoDocumento::class, 'tipos-documentos', 'tipo de documento');
    }

    public function create(Request $request)
    {
        $caducidades = config('enums.tipos_caducidad');
        $doc_scopes = config('enums.doc_scopes');

        return parent::__create($request, compact('caducidades', 'doc_scopes'));
    }

    // public function store(Request $request)

    public function edit(Request $request, $id)
    {
        $caducidades = config('enums.tipos_caducidad');
        $doc_scopes = config('enums.doc_scopes');

        return parent::__edit($request, $id, compact('caducidades', 'doc_scopes'));
    }

    // public function update(Request $request, $id)

    // public function remove($id)

    public function rowsData(Request $request)
    {
        $archive = request()->get('h');

        $documentos = TipoDocumento::select(['id', 'nombre', 'referencia', 'notas', 'ambito', 'tipo_caducidad', 'activo'])
                                    ->where('activo', '=', !$archive);

        // Filtro por ÁMBITO
        if (isset($request['a'])) {
            $ambito = $request->get('a');
            // Pueden venir varios. P.e. 'EMP,CEN'
            $ambitos = explode(",", $ambito);
            // Filtramos cada ámbito
            $first = true;
            foreach ($ambitos as $amb) {
                if ($first == true) {
                    $documentos->where('ambito', '=', $amb);
                    $first = false;
                } else {
                    $documentos->orWhere('ambito', '=', $amb);
                }
            }
        }

        $datatable = $this->defaultDatatable($documentos, $archive);

        return $datatable->make(true);
    }

    private function defaultDatatable($documentos, $archive)
    {
        $datatable = Datatables::of($documentos)
                        ->setRowId('id')
                        ->addColumn('actions', function ($documento) use ($archive) {
                            return $this->getActionColumn($documento, true, $archive);
                        })
                        ->editColumn('ambito', function ($documento) {
                            return config('enums.doc_scopes')[$documento->ambito];
                        })
                        ->editColumn('tipo_caducidad', function ($documento) {
                            return config('enums.tipos_caducidad')[$documento->tipo_caducidad];
                        })
                        ->addColumn('tags', function ($documento) {
                            return $this->getTagsColumn($documento);
                        })
                        ->rawColumns(['actions', 'tags']);

        return $datatable;
    }

    protected function validator(array $data)
    {
        $validator = Validator::make($data, [
            'nombre' => 'required|max:100',
            'ambito' => 'required',
            'referencia' => 'max:50',
            'tipo_caducidad' => 'required'
        ]);

        $fields_names = [
            'nombre' => 'Nombre',
            'ambito' => 'Ámbito',
            'referencia' => 'Referencia',
            'tipo_caducidad' => 'Tipo Caducidad',
        ];
        $validator->setAttributeNames($fields_names);

        return $validator;
    }
}
