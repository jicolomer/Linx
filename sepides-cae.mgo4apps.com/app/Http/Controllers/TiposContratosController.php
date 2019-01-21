<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;

use Validator;

use Yajra\Datatables\Datatables;

use App\Models\TipoContrato;

class TiposContratosController extends Documentos\TiposDocumentosBaseController
{
    // TiposDocumentosBaseController
    protected $tdt_pivot_table_name = 'tipos_contratos_doc';
    protected $tdt_pivot_table_main_id_name = 'tipo_contrato_id';


    public function __construct()
    {
        parent::__construct(TipoContrato::class, 'tipos-contratos', 'tipo de contrato');
    }

    // public function index()

    // public function create()

    public function store(Request $request)
    {
        $nivel_subcontratas = false;

        if ($request->exists('nivel_subcontratas')) {
            $nivel_subcontratas = true;
        }

        return parent::__store($request, compact('nivel_subcontratas'));
    }

    // public function edit($id)  // -> TiposDocumentosBaseController

    public function update(Request $request, $id)
    {
        $nivel_subcontratas = $request->exists('nivel_subcontratas');

        return parent::__update($request, $id, compact('nivel_subcontratas'));
    }

    // public function remove($id)

    public function rowsData(Request $request)
    {
        $archive = request()->get('h');

        $tipos = TipoContrato::select([ 'id', 'nombre', 'notas', 'nivel_subcontratas', 'activo' ])
                        ->where('activo', '=', !$archive);

        return Datatables::of($tipos)
            ->addColumn('actions', function ($tipo) use ($archive) {
                return $this->getActionColumn($tipo, true, $archive);
            })
            ->removeColumn('nivel_subcontratas')
            ->addColumn('nivelSubcontratas', function ($tipo) {
                return $this->getCheckColumn($tipo->nivel_subcontratas);
            })
            ->rawColumns(['actions', 'nivelSubcontratas'])
            ->make(true);
    }

    protected function validator(array $data, $isUpdate = false)
    {
        $validator = Validator::make($data, [
            'nombre' => 'required|max:100|' . ($isUpdate ? 'unique:tipos_contratos,nombre,'.$data['id'] : 'unique:tipos_contratos'),
        ]);

        $fields_names = [
            'nombre' => 'Nombre',
        ];
        $validator->setAttributeNames($fields_names);

        return $validator;
    }
}
