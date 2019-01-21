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

class ContratosContratistaController extends ContratosContratistaBaseController
{
    use ContratosDocumentacionTrait, DocumentosBaseTrait;

    public function editContratista(Request $request, $contrato_id, $contratista_id)
    {
        return $this->editView($request, $contrato_id, $contratista_id);
    }

    public function editSubcontratista(Request $request, $contrato_id, $contratista_id, $subcontratista_id)
    {
        return $this->editView($request, $contrato_id, $contratista_id, $subcontratista_id);
    }

    private function editView(Request $request, $contrato_id, $contratista_id, $subcontratista_id = false)
    {
        Session::put(self::CURRENT_URL_KEY, url()->current());
        Session::put($this->editing_id_session_key, $contrato_id);
        Session::put(self::EDITANDO_CONTRATISTA_KEY, $contratista_id);
        Session::put(self::EDITANDO_SUBCONTRATISTA_KEY, $subcontratista_id);

        $return_to = $request->get('r');
        if ($return_to == null) {
            if ($default_return_to = Session::get('default_return_to')) {
                $return_to = $this->getReturnTo();
            } else {
                if ($subcontratista_id == false) {
                    $return_to = route('contratos.edit', $contrato_id);
                } else {
                    $return_to = route('contratos.contratista', [$contrato_id, $contratista_id]);
                }
            }
        } else {
            Session::put($this->return_to_url_session_key, $return_to);
            if ($subcontratista_id == false) {
                return redirect(route('contratos.contratista', [$contrato_id, $contratista_id]))->withDefaultReturnTo(true);
            } else {
                return redirect(route('contratos.subcontratista', [$contrato_id, $contratista_id, $subcontratista_id]))->withDefaultReturnTo(true);
            }
        }

        $contrato = $this->currentContrato();

        $empresa_id = $this->getEmpresaIdEditando();
        $cabecera = 'Contratista: <strong>' . Empresa::getNombreEmpresa($contratista_id) . '</strong><br />';
        $subcontratista = ($subcontratista_id != false);
         if ($subcontratista) {
            $cabecera .= 'Subcontratista: <strong>' . Empresa::getNombreEmpresa($subcontratista_id) . '</strong><br />';
        }

        // Doc. Contratista
        $tipos_documentos = TipoDocumento::where('ambito', 'CTA')->pluck('nombre', 'id');

        $status_doc = $this->getDocStatusColumn($contrato->statusDocContratista($empresa_id));
        $status_doc_trabajadores = $this->getDocStatusColumn($contrato->statusDocTrabajadoresContratista($empresa_id));
        $status_doc_maquinas = $this->getDocStatusColumn($contrato->statusDocMaquinasContratista($empresa_id));

        return view('contratos.contratista',
                    compact('return_to', 'contrato', 'empresa_id',
                            'cabecera', 'subcontratista', 'tipos_documentos',
                            'status_doc', 'status_doc_trabajadores', 'status_doc_maquinas'
                    )
                );
    }


    // *************************************************************************
    // DOCUMENTOS
    // *************************************************************************

    // Añade, Modifica o crea nueva Versión de un documento del contratista
    // URL: POST contratos/edit/{contrato_id}/contratista/{contratista_id}/documentos/add
    public function addDocumentoContratista(Request $request, $contrato_id, $contratista_id)
    {
        // Llamamos al método en el trait DocumentosBaseTrait
        return $this->__addDocumento($request, Empresa::class, $contratista_id);
    }

    // Devuelve los datos de un documento del contratista para editarlo
    // URL: contratos/documentos/{id}/data
    public function getDocumentoDataContratista(Request $request, $contrato_id, $contratista_id, $documento_id)
    {
        // Llamamos al método en el trait DocumentosBaseTrait
        return $this->__getDocumentoData($request, $documento_id, Empresa::class);
    }

    // Añade, Modifica o crea nueva Versión de un documento del subcontratista
    // URL: POST contratos/edit/{contrato_id}/contratista/{contratista_id}/subcontratista/{subcontratista_id}/documentos/add
    public function addDocumentoSubcontratista(Request $request, $contrato_id, $contratista_id, $subcontratista_id)
    {
        // Llamamos al método en el trait DocumentosBaseTrait
        return $this->__addDocumento($request, Empresa::class, $subcontratista_id);
    }

    // Devuelve los datos de un documento de la empresa principal para editarlo
    // URL: contratos/documentos/{id}/data
    public function getDocumentoDataSubcontratista(Request $request, $contrato_id, $contratista_id, $subcontratista_id, $documento_id)
    {
        // Llamamos al método en el trait DocumentosBaseTrait
        return $this->__getDocumentoData($request, $documento_id, Empresa::class);
    }

}
