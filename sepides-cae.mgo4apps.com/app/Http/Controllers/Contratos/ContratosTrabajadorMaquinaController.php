<?php
namespace App\Http\Controllers\Contratos;

use Illuminate\Http\Request;
use Auth;
use Session;
use URL;
use Validator;

use Jenssegers\Date\Date;
use Yajra\Datatables\Datatables;

use App\Http\Controllers\Documentos\DocumentosBaseTrait;
use App\Models\Contrato;
use App\Models\ContratoMaquina;
use App\Models\ContratoTrabajador;
use App\Models\Empresa;
use App\Models\Trabajador;
use App\Models\Maquina;
use App\Models\Aviso;
use App\Models\TipoDocumento;

class ContratosTrabajadorMaquinaController extends ContratosContratistaBaseController
{
    use ContratosDocumentacionTrait, DocumentosBaseTrait;

    public function editTrabajador(Request $request, $contrato_id, $trabajador_id)
    {
        return $this->editView($request, $contrato_id, $trabajador_id, true);
    }

    public function editMaquina(Request $request, $contrato_id, $maquina_id)
    {
        return $this->editView($request, $contrato_id, $maquina_id, false);
    }

    private function editView(Request $request, $contrato_id, $item_id, $isTrabajador)
    {
        $return_to = $request->get('r');
        if ($return_to == null) {
            $return_to = $this->getReturnTo();
        } else {
            Session::put($this->return_to_url_session_key, $return_to);
            return redirect(route($isTrabajador ? 'contratos.trabajador' : 'contratos.maquina', [$contrato_id, $item_id]))->withDefaultReturnTo(true);
        }

        $contrato = $this->currentContrato();
        $contratista_id = Session::get(self::EDITANDO_CONTRATISTA_KEY);
        $subcontratista_id = Session::get(self::EDITANDO_SUBCONTRATISTA_KEY);

        $cabecera = 'Contratista: <strong>' . Empresa::getNombreEmpresa($contratista_id) . '</strong><br />';
        $subcontratista = ($subcontratista_id != false);
         if ($subcontratista) {
            $cabecera .= 'Subcontratista: <strong>' . Empresa::getNombreEmpresa($subcontratista_id) . '</strong><br />';
        }

        if ($isTrabajador) {
            Session::put(self::EDITANDO_TRABAJADOR_KEY, $item_id);
            Session::forget(self::EDITANDO_MAQUINA_KEY);
            $item_name = Trabajador::getNombreTrabajador($item_id, true);
            $cabecera .= 'Trabajador: <strong>' . $item_name . '</strong><br />';
            $status_doc = $this->getDocStatusColumn($contrato->statusDocTrabajador($item_id));
        } else {
            Session::put(self::EDITANDO_MAQUINA_KEY, $item_id);
            Session::forget(self::EDITANDO_TRABAJADOR_KEY);
            $item_name = Maquina::getNombreMaquina($item_id);
            $cabecera .= 'Máquina: <strong>' . $item_name . '</strong><br />';
            $status_doc = $this->getDocStatusColumn($contrato->statusDocMaquina($item_id));
        }

        // Doc. Trabajador/Maquina
        $tipos_documentos = TipoDocumento::where('ambito', $isTrabajador ? 'TRA' : 'MAQ')->pluck('nombre', 'id');

        return view('contratos.trabajadormaquina',
                    compact('return_to', 'contrato', 'item_id', 'item_name',
                            'tipos_documentos', 'cabecera', 'isTrabajador', 'status_doc'
                    )
        );
    }


    // *************************************************************************
    // DOCUMENTOS
    // *************************************************************************

    // Añade, Modifica o crea nueva Versión de un documento del trabajador
    // POST contratos/edit/{contrato_id}/trabajador/{trabajador_id}/documentos/add
    public function addDocumentoTrabajador(Request $request, $contrato_id, $trabajador_id)
    {
        // Llamamos al método en el trait DocumentosBaseTrait
        return $this->__addDocumento($request, Trabajador::class, $trabajador_id);
    }

    // Devuelve los datos de un documento del trabajador para editarlo
    // GET contratos/edit/{contrato_id}/trabajador/{trabajador_id}/documentos/{documento_id}/data
    public function getDocumentoDataTrabajador(Request $request, $contrato_id, $trabajador_id, $documento_id)
    {
        // Llamamos al método en el trait DocumentosBaseTrait
        return $this->__getDocumentoData($request, $documento_id, Trabajador::class);
    }

    // Añade, Modifica o crea nueva Versión de un documento de la máquina
    // POST contratos/edit/{contrato_id}/maquina/{maquina_id}/documentos/add
    public function addDocumentoMaquina(Request $request, $contrato_id, $maquina_id)
    {
        // Llamamos al método en el trait DocumentosBaseTrait
        return $this->__addDocumento($request, Maquina::class, $maquina_id);
    }

    // Devuelve los datos de un documento de la máquina para editarlo
    // GET contratos/edit/{contrato_id}/maquina/{maquina_id}/documentos/{documento_id}/data
    public function getDocumentoDataMaquina(Request $request, $contrato_id, $maquina_id, $documento_id)
    {
        // Llamamos al método en el trait DocumentosBaseTrait
        return $this->__getDocumentoData($request, $documento_id, Maquina::class);
    }

}
