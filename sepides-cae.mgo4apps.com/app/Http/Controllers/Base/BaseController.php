<?php

namespace App\Http\Controllers\Base;

use Illuminate\Http\Request;
use App\Http\Requests;

use Auth;
use Session;
use Route;
use Illuminate\Support\Str;

use Laracasts\Flash\Flash;

use App\Models\Centro;
use App\Models\Empresa;
use App\User;

class BaseController extends \App\Http\Controllers\Controller
{
    // The controller default route...
    protected $base_route = null;

    // The base views path
    protected $views_path = null;

    // Nombre de la key de sessión donde se almacena el id que se está editando
    protected $editing_id_session_key = null;

    // Modelo de la tabla principal (p.e 'App\Models\Usuario')
    protected $model = null;

    // Nombre del modelo (p.e. 'usuario'). Usado como nombre de variable del modelo.
    // Se extrae de views_path, quitando la última 's'.
    private $model_variable_name = 'model';

    private $model_display_name;
    private $gender_letter;

    private $model_taggable = false;

    protected $return_to_url_session_key = null;

    protected $validator_instance = null;


    /***************************************************************************
     *  Constructor
     **************************************************************************/
    public function __construct($model = null, $base_route = null, $model_display_name = null, $views_path = null)
    {
        parent::__construct();

        $this->model = $model;
        $this->base_route = $base_route;
        $this->model_display_name = $model_display_name;
        $this->views_path = $views_path;

        $this->setVariables();
    }

    private function setVariables()
    {
        // Comprobamos si el modelo es 'taggable'
        $traits = class_uses($this->model);
        $this->model_taggable = array_key_exists("Cviebrock\EloquentTaggable\Taggable", $traits);

        // El views_path lo componemos a partir de base_route (si no viene)
        if ($this->views_path == null) {
            $this->views_path = str_replace('-', '_', $this->base_route);
        }

        $this->editing_id_session_key = $this->base_route . '-editing-id-key';
        $this->return_to_url_session_key = $this->base_route . '-backURL-key';

        if ($this->model_display_name == null) {
            $this->model_display_name = substr($this->views_path, 0, -1);
        }

        $letter = substr($this->model_display_name, -1);
        if (($letter != 'o') && ($letter != 'a')) {
            $letter = substr($this->model_display_name, -2, 1);
        }
        $this->gender_letter = $letter;
    }




    /***************************************************************************
     *  INDEX
     **************************************************************************/
    protected function index(Request $request)
    {
        return $this->__index($request);
    }

    public function __index(Request $request, $external_data = null)
    {
        $this->setReturnTo();
        Session::forget($this->editing_id_session_key);

        if ($external_data != null) {
            return view($this->views_path . '.index', $external_data);
        } else {
            return view($this->views_path . '.index');
        }
    }




    /***************************************************************************
     *  CREATE
     **************************************************************************/
    // Usar esta cuando no haya nada que añadir a la vista.
    public function create(Request $request)
    {
        return $this->__create($request);
    }

    // Usar esta cuando haya que añadir variables a la vista.
    public function __create(Request $request, $external_data = null)
    {
        if ($return_to = $request->get('r')) {
            $this->setReturnTo();
        }

        $return_to = $this->getReturnTo();
        $data = compact('return_to');

        if ($this->model_taggable) {
            $data = array_merge($data, ['tags' => []]);
        }

        if ($external_data != null) {
            $data = array_merge($data, $external_data);
        }

        return view($this->views_path.'.create', $data);
    }




    /***************************************************************************
     *  STORE
     **************************************************************************/
    // Esto se usa por defecto si no hay nada especial.
    public function store(Request $request)
    {
        return $this->__store($request);
    }

    // Usar esta cuando haya que añadir datos o comprobaciones previas.
    public function __store(Request $request, $data_array = [])
    {
        $instance = $this->__store_create_record($request, $data_array);

        // Error en validación
        if ($instance == false) {
            return redirect()->back()
                        ->with('errors', $this->validator_instance->errors())
                        ->withInput();
        }

        return $this->__store_return();
    }

    // Usar cuando hay que hacer algo justo después de guardar y antes de regresar
    public function __store_create_record(Request $request, $data_array = [])
    {
        $fields = $request->all();

        $this->validator_instance = $this->validator($fields);
        if ($this->validator_instance->fails()) {
            return false;
        }

        $fields = array_merge($fields, $data_array);

        $model = $this->model;
        $instance = $model::create($fields);

        // Tags
        if ($this->model_taggable) {
            if ($tags = $request->get('tags')) {
                $instance->tag(explode(',', $tags));
            }
        }

        return $instance;
    }

    // Último paso
    public function __store_return($msg = '')
    {
        $this->setFlashMessageAfterSuccessfulStore($msg);

        return $this->whereToReturn();
    }

    private function setFlashMessageAfterSuccessfulStore($msg = '')
    {
        $m = 'Nuev' . $this->gender_letter . ' ' . $this->model_display_name . ' cread' . $this->gender_letter . '.';

        if (strlen($msg) > 0) {
            $m .= '<br>' . $msg;
        }

        Flash::success($m);
    }




    /***************************************************************************
     *  EDIT
     **************************************************************************/
    // Esto se usa por defecto si no hay nada especial.
    public function edit(Request $request, $id)
    {
        return $this->__edit($request, $id);
    }

    // Usar esta cuando haya que añadir datos o comprobaciones previas.
    public function __edit(Request $request, $id, $external_data = null)
    {
        if ($return_to = $request->get('r')) {
            $this->setReturnTo();
        }

        Session::put($this->editing_id_session_key, $id);

        if (isset($external_data['return_to'])) {
            $return_to = $external_data['return_to'];
            if ($return_to == 'null') {
                $return_to = null;
            };
            unset($external_data['return_to']);
        } else {
            $return_to = $this->getReturnTo();
        }

        $data = compact($return_to == null ? '' : 'return_to');

        $model = $this->model;
        $instance = $model::findOrFail($id);
        $data = array_merge($data, [$this->model_variable_name => $instance]);

        // TAGS
        if ($this->model_taggable) {
            $tags = $instance->tagListNormalized;
            $data = array_merge($data, compact('tags'));
        }

        if ($external_data != null) {
            $data = array_merge($data, $external_data);
        }

        return view($this->views_path.'.edit', $data);
    }




    /***************************************************************************
     *  UPDATE
     **************************************************************************/
    // Esto se usa por defecto si no hay nada especial.
    public function update(Request $request, $id)
    {
        return $this->__update($request, $id);
    }

    // Usar esta cuando haya que añadir datos o comprobaciones previas.
    public function __update(Request $request, $id, $data_array = [])
    {
        $instance = $this->__update_save_record($request, $id, $data_array);

        // Error en validación
        if ($instance == false) {
            return redirect()->back()
                        ->with('errors', $this->validator_instance->errors())
                        ->withInput();
        }

        return $this->__update_return();
    }

    // Usar cuando hay que hacer algo justo después de guardar y antes de regresar
    public function __update_save_record(Request $request, $id, $data_array = [])
    {
        $fields = $request->all();

        $this->validator_instance = $this->validator($fields, true);
        if ($this->validator_instance->fails()) {
            return false;
        }

        $fields = array_merge($fields, $data_array);

        $model = $this->model;
        $instance = $model::findOrFail($id);
        $instance->fill($fields)->save();

        // TAGS
        if ($this->model_taggable) {
            if ($tags = $request->get('tags')) {
                $instance->retag(explode(',', $tags));
            }
        }

        return $instance;
    }

    // Último paso
    public function __update_return($msg = '')
    {
        $this->setFlashMessageAfterSuccessfulUpdate($msg);

        return redirect()->back();
    }

    private function setFlashMessageAfterSuccessfulUpdate($msg = '')
    {
        $m = 'Datos de' . ($this->gender_letter == 'o' ? 'l ' : ' la ') . $this->model_display_name . ' modificados.';

        if (strlen($msg) > 0) {
            $m .= '<br>' . $msg;
        }

        Flash::success($m);
    }




    /**************************************************************************
     *  REMOVE
     *************************************************************************/
    // Esto se usa por defecto si no hay nada especial.
    public function remove($id)
    {
        return $this->__remove($id);
    }

    // Usar esta cuando haya que añadir datos o comprobaciones previas.
    public function __remove($id)
    {
        $model = $this->model;
        if ($instance = $model::where('id', '=', $id)->first()) {
            $instance->activo = false;
            $instance->save();

            return array('result' => 'success');
        }

        return array('result' => 'error');
    }



    /**************************************************************************
     *  REMOVE
     *************************************************************************/
    // Esto se usa por defecto si no hay nada especial.
    public function restore($id)
    {
        return $this->__restore($id);
    }

    // Usar esta cuando haya que añadir datos o comprobaciones previas.
    public function __restore($id)
    {
        $model = $this->model;
        if ($instance = $model::where('id', '=', $id)->first()) {
            $instance->activo = true;
            $instance->save();

            return array('result' => 'success');
        }

        return array('result' => 'error');
    }




    /***************************************************************************
     *  HELPER FUNCTIONS
     **************************************************************************/
    /*
     *  Tells the controller where to return
     */
    public function whereToReturn()
    {
        if ($return_to = $this->getReturnTo()) {
            return redirect($return_to);
        } else {
            return redirect($this->base_route);
        }
    }

    public function setReturnTo()
    {
        $url = request()->fullUrl();

        if ($return_to = request()->get('r')) {
            $url = $return_to;
        }

        Session::put($this->return_to_url_session_key, $url);

        return $url;
    }

    /*
     *  Gets the return_to url from Session (if exists!)
     */
    public function getReturnTo()
    {
        if ($return_to = Session::get($this->return_to_url_session_key)) {
            return $return_to;
        } else {
            return null;
        }
    }

    /*
     *  Gets the empresa_id ('e' param) from request params
     */
    public function getEmpresaIdFromRequest(Request $request)
    {
        $empresa_id = $request->get('e');
        if ($empresa_id != null) {
            $empresa_id = intval($empresa_id);
        }
        return $empresa_id;
    }

    /*
     *  The currend editing item ID
     */
    protected function getEditingId()
    {
        return Session::get($this->editing_id_session_key);
    }


    /***************************************************************************
     *  DATATABLE COLUMNS HELPER FUNCTIONS
     **************************************************************************/
    /*
     *  Return the default 'actions' column for index Datatables
     */
    protected function getActionColumn($model, $withRemove = true, $archive = false)
    {
        $html =  '<div class="btn-group">';
        if ($archive) {
            $html .= '<button type="button" class="btn btn-default btn-sm restore-button" data-id='. $model->id . ' data-toggle="tooltip" title="Recuperar"><i class="fa fa-undo text-red"></i></button>';
        } else {
            $html .= '<button type="button" class="btn btn-default btn-sm" data-toggle="tooltip" title="Ver/Editar"><i class="fa fa-pencil"></i></button>';
            if ($withRemove == true) {
                $html .= '<button type="button" class="btn btn-default btn-sm delete-button" data-id='. $model->id . ' data-toggle="tooltip" title="Archivar"><i class="fa fa-times text-red"></i></button>';
            }
        }
        $html .= '</div>';

        return $html;
    }

    /*
     *  Return the right symbol (thumb up, cross, number) with the corresponding color (gree, red, orange) for the $status
     */
    protected function getDocStatusColumn($status)
    {
        switch ($status) {
            // Documento RECHAZADO
            case -5:
                return '<span data-toggle="tooltip" data-placement="left" data-html="true" title="Hay al menos un documento<br />RECHAZADO"><i class="fa fa-lg fa-thumbs-down text-red"></i></span>';
                break;
            // Documento NO VALIDADO
            case -4:
                return '<span data-toggle="tooltip" data-placement="left" data-html="true" title="Hay al menos un documento<br />NO VALIDADO"><i class="fa fa-lg fa-ban text-dark-gray"></i></span>';
                break;
            // Documento CADUCADO
            case -3:
                return '<span class="badge bg-red" data-toggle="tooltip" data-placement="left" data-html="true" title="Hay al menos un documento<br />CADUCADO"><i class="fa fa-times"></i></span>';
                break;
            // Falta algún documento OBLIGATORIO
            case -2:
                return '<span data-toggle="tooltip" data-placement="left" data-html="true" title="Falta al menos un documento<br />OBLIGATORIO"><i class="fa fa-lg fa-exclamation-triangle text-orange"></i></span>';
                break;
            // No hay documentos
            case -1:
                return "";
                break;
            // Documentación OK
            case 0:
                return '<span class="badge bg-green" data-toggle="tooltip" data-placement="left" title="Documentación OK"><i class="fa fa-thumbs-up"></i></span>';
                break;
            // Documento con caducidad próxima
            default:
                return '<span class="badge bg-orange" data-toggle="tooltip" data-placement="left" title="Faltan ' . $status . ' días para que caduque un documento">-'. $status .'</span>';
                break;
        }
    }

    /*
     *  Return the right symbol (thumb up, cross, number) with the corresponding color (green, red, orange) for the $status
     */
    protected function getCaducidadStatusColumn($status)
    {
        switch ($status) {
            case -2:
                return '';
                break;
            case -1:
                return '<span class="badge bg-green" data-toggle="tooltip" data-placement="right" title="OK"><i class="fa fa-thumbs-up"></i></span>';
                break;
            case 0:
                return '<span class="badge bg-red" data-toggle="tooltip" data-placement="right" title="¡Documento Caducado!"><i class="fa fa-times"></i></span>';
                break;
            default:
                return '<span class="badge bg-orange" data-toggle="tooltip" data-placement="right" title="¡Faltan ' . $status . ' días para que caduque el documento!">-'. $status .'</span>';
                break;
        }
    }

    /*
     *  Return the right symbol (ban, thumb up, thumb down) with the corresponding color (gray, green, red) for the $status
     */
    protected function getValidacionStatusColumn($status, $validacion = null)
    {
        if ($status == 0) {
            return '<span class="text-dark-gray" data-toggle="tooltip" data-placement="right" title="Pendiente de validación"><i class="fa fa-lg fa-ban"></i></span>';
        } else {
            $title = ($status==1) ? 'Documento <strong>aprobado</strong>' : '¡Documento <strong>rechazado</strong>!';
            if ($validacion) {
                $title .= '<br />Fecha: <strong>' . $validacion->fecha_revision . '</strong>';
                $user = User::find($validacion->usuario_id);
                if ($user) {
                    $title .= '<br />Responsable: <strong>' . $user->nombre . '</strong>';
                }
                if ($status == -1 && !is_null($validacion->notas)) {
                    $title .= '<br />Motivo: <strong>' . Str::words($validacion->notas, 10) . '</strong>';
                }
            }
            $color = ($status==1) ? 'green' : 'red';
            $icon = ($status==1) ? 'up' : 'down';

            return '<span class="text-' . $color . '" data-toggle="tooltip" data-html="true" data-placement="right" title="' . $title . '"><i class="fa fa-lg fa-thumbs-' . $icon . '"></i></span>';
        }
    }

    /*
     *  Returns the green checkmark if the $condition is true
     */
    protected function getCheckColumn($condition)
    {
        if ($condition == true) {
            return '<p style="font-size:18px"><i class="fa fa-check text-green"></i><p>';
        } else {
            return '';
        }
    }

    /*
     *  Returns the tags column
     */
    public function getTagsColumn($documento)
    {
        $html = '';
        foreach ($documento->tags as $tag) {
            $html .= '<span class="label label-success">' . $tag->normalized . '</span> ';
        }
        return $html;
    }

    /*
     * Returns the centro_id column with additional centro's info
     */
    protected function getCentroColumn($centro_id)
    {
        if ($centro_id != null) {
            $centro = Centro::findOrFail($centro_id);
            $data = '<strong>' . $centro->nombre . '</strong><br />' .
                    'Teléfono: <strong>' . $centro->telefono_centro . '</strong>';
            return $centro_id . '&nbsp;&nbsp;' .
                '<i class="fa fa-plus-circle text-dark-gray" data-toggle="tooltip" data-placement="right" data-html="true" data-title="' .
                $data . '"></i>';
        } else {
            return ' ';
        }
    }
}
