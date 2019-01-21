<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;

use Validator;
use Session;

use Yajra\Datatables\Datatables;

use App\Models\Centro;
use App\Models\CentroDoc;
use App\Models\Documento;
use App\Models\TipoDocumento;
use App\Models\Provincia;

class CentrosController extends Documentos\DocumentosBaseController
{
    public function __construct()
    {
        parent::__construct(Centro::class, 'centros');
    }

    // public function index()

    public function create(Request $request)
    {
        $provincias = Provincia::pluck('nombre', 'id');

        return parent::__create($request, compact('provincias'));
    }

    // public function store(Request $request)

    public function edit(Request $request, $id)
    {
        $provincias = Provincia::pluck('nombre', 'id');
        $ambito = 'CEN';

        return parent::__edit($request, $id, compact('provincias', 'ambito'));
    }

    // public function update(Request $request, $id)

    // public function remove($id)

    public function rowsData()
    {
        $centros = Centro::select(['*'])->where('activo', '=', true);

        return Datatables::of($centros)
                    ->setRowId('id')
                    ->addColumn('actions', function ($centro) {
                        return $this->getActionColumn($centro);
                    })
                    ->rawColumns(['actions'])
                    ->make(true);
    }

    protected function validator(array $data)
    {
        $validator = Validator::make($data, [
            'nombre' => 'required|max:255',
            'codigo_postal' => 'digits:5',
            'municipio' => 'max:100',
            'telefono_centro' => 'required|digits:9',
            'fax_centro' => 'digits:9',
            'email_centro' => 'email|max:255',
            'persona_contacto' => 'max:255',
            'telefono_contacto' => 'digits:9',
            'email_contacto' => 'email|max:255',
        ]);

        $fields_names = [
            'nombre' => 'Nombre',
            'codigo_postal' => 'Código Postal',
            'municipio' => 'Municipio',
            'telefono_centro' => 'Teléfono del Centro',
            'fax_centro' => 'Fax del Centro',
            'email_centro' => 'Email del Centro',
            'persona_contacto' => 'Persona de Contacto',
            'telefono_contacto' => 'Teléfono del Contacto',
            'email_contacto' => 'Email del Contacto',
        ];
        $validator->setAttributeNames($fields_names);

        return $validator;
    }
}
