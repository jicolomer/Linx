<?php

namespace App\Http\Controllers\Documentos;

use Illuminate\Http\Request;
use App\Http\Requests;

use Laracasts\Flash\Flash;
use Yajra\Datatables\Datatables;

use App\Models\Documento;
use App\Models\TipoDocumento;


class TiposDocumentosBaseController extends \App\Http\Controllers\Base\BaseController
{
    use TiposDocumentosTrait;

    /*
     * Clase base para los controllers que implementen la asociación del modelo principal
     * con Tipos de Documentos.
     *
     */

    public function edit(Request $request, $id)
    {
        $tipos_documentos_add_route = route($this->base_route . '.addTiposDocumento');

        return parent::__edit($request, $id, compact('tipos_documentos_add_route'));
    }

}
