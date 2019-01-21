<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;

use Validator;

use Yajra\Datatables\Datatables;

use App\Models\TipoMaquina;

class TiposMaquinasController extends Documentos\TiposDocumentosBaseController
{
    // TiposDocumentosBaseController
    protected $tdt_pivot_table_name = 'tipos_maquinas_doc';
    protected $tdt_pivot_table_main_id_name = 'tipo_maquina_id';


    public function __construct()
    {
        parent::__construct(TipoMaquina::class, 'tipos-maquinas', 'tipo de máquina');
    }

    // public function index()

    // public function create()

    // public function store(Request $request)

    // public function edit($id)  // -> TiposDocumentosBaseController

    // public function update(Request $request, $id)

    // public function remove($id)

    public function rowsData(Request $request)
    {
        $tipos = TipoMaquina::select([ 'id', 'nombre', 'notas' ]);

        return Datatables::of($tipos)
            ->addColumn('actions', function ($tipo) {
                return $this->getActionColumn($tipo);
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    protected function validator(array $data)
    {
        $validator = Validator::make($data, [
            'nombre' => 'required|max:100',
        ]);

        $fields_names = [
            'nombre' => 'Nombre',
        ];
        $validator->setAttributeNames($fields_names);

        return $validator;
    }
}
