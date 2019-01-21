<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
*/
use \App\Http\Controllers\Base\BaseRouter;
use \App\Http\Controllers\Documentos\DocumentosRouter;
use Illuminate\Http\Request;
use App\Http\Requests;
use Laracasts\Flash\Flash;

// AUTH
Route::get('check-session', 'Auth\LoginController@checkSession');
Route::get('login', 'Auth\LoginController@showLoginForm');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');

// HOME
Route::get('/', ['as' => 'home', 'uses' => 'DashboardController@index']);
Route::get('avisos/data', ['as' => 'avisos.data', 'uses' => 'DashboardController@avisosData']);
Route::get('avisos/go/{id}', ['as' => 'avisos.go', 'uses' => 'DashboardController@avisosGo']);

// UTILS - API
Route::group(['middleware' => 'role:administrador'], function () {
    Route::get('api/utils/documentos-zombi', ['uses' => 'APIUtilsController@documentosZombi']);
    Route::get('api/utils/documento/{filename}', ['uses' => 'APIUtilsController@documentoMostrar']);
    Route::get('api/utils/documento/{filename}/delete', ['uses' => 'APIUtilsController@documentoEliminar']);
    Route::get('api/utils/phpinfo', function (){
        return phpinfo();
    });
});

// DOCUMENTOS - API
Route::post('api/documentos/valida-documento', ['as' => 'documento.validado', 'uses' => 'Documentos\APIDocumentosController@documentoValidado'])->middleware('permission:documentos.validar');
Route::get('api/documentos/tipo-documento-tags/{tipo_documento_id}', ['as' => 'tipo_documento_tags', 'uses' => 'Documentos\APIDocumentosController@tipoDocumentoTags']);
Route::get('api/documentos/versiones/{id}', ['as' => 'versiones_documento', 'uses' => 'Documentos\APIDocumentosController@versiones']);
Route::get('api/documentos/caducidad', ['as' => 'caducidad_documento', 'uses' => 'Documentos\APIDocumentosController@caducidad']);
Route::get('api/documentos/download/{id}', ['as' => 'getfile', 'uses' => 'Documentos\APIDocumentosController@download']);
Route::get('api/documentos/zip', ['as' => 'getZip', 'uses' => 'Documentos\APIDocumentosController@downloadZip']);
Route::get('api/documentos/data/{id}', ['as' => 'document_data', 'uses' => 'Documentos\APIDocumentosController@getDocumentoData']);

// USUARIOS
Route::get('usuarios/empresa/{id}', ['as' => 'usuarios.empresa', 'uses' => 'UsuariosController@json_ListaUsuariosEmpresa']);
Route::get('usuarios/block/{id}', ['as' => 'usuarios.block', 'uses' => 'UsuariosController@blockUser'])->middleware('canAtLeast:usuarios.update,usuarios-global.update');
Route::get('usuarios/reset-password/{id}', ['as' => 'usuarios.resetPassword', 'uses' => 'UsuariosController@resetPassword'])->middleware('canAtLeast:usuarios.update,usuarios-global.update');
BaseRouter::basicRoutes('usuarios', 'UsuariosController', ['usuarios', 'usuarios-global']);
// PERMISOS
Route::get('permisos', ['as' => 'permisos', 'uses' => 'UsuariosController@permisosIndex'])->middleware('permission:permisos.view');
Route::get('permisos/data', ['as' => 'permisos.data', 'uses' => 'UsuariosController@permisosData'])->middleware('permission:permisos.view');
Route::post('permisos', ['as' => 'permisos.update', 'uses' => 'UsuariosController@cambiaPermiso'])->middleware('permission:permisos.update');

// TIPOS DOCUMENTOS
BaseRouter::basicRoutes('tipos-documentos', 'TiposDocumentosController', 'tipos-documentos');

// TIPOS MAQUINAS
DocumentosRouter::tiposDocumentos('tipos-maquinas', 'TiposMaquinasController', 'tipos-maquinas');

// TIPOS CONTRATOS
DocumentosRouter::tiposDocumentos('tipos-contratos', 'TiposContratosController', 'tipos-contratos');

// MAQUINAS
Route::get('maquinas/docFaltanteData', ['as' => 'maquinas.docFaltanteData', 'uses' => 'MaquinasController@docFaltanteData'])->middleware('canAtLeast:maquinas.view,maquinas-global.view');
DocumentosRouter::documentos('maquinas', 'MaquinasController', ['maquinas', 'maquinas-global']);

// TRABAJADORES
Route::get('trabajadores/empresa/{id}', ['as' => 'trabajadores.empresaUsuarios', 'uses' => 'TrabajadoresController@json_ListaTrabajadoresUsuariosEmpresa']);
Route::get('trabajadores/create-user/{id}', ['as' => 'trabajadores.nuevoUsuario', 'uses' => 'TrabajadoresController@nuevoUsuario'])->middleware('canAtLeast:usuarios.create,usuarios-global.create');
DocumentosRouter::documentos('trabajadores', 'TrabajadoresController', ['trabajadores', 'trabajadores-global']);

// EMPRESAS
Route::get('empresa', ['as' => 'empresa', 'uses' => 'EmpresasController@editEmpresa'])->middleware('canAtLeast:empresas.view,empresas-global.view');
DocumentosRouter::documentos('empresas', 'EmpresasController', ['empresas', 'empresas-global']);

// CENTROS DE TRABAJO
DocumentosRouter::documentos('centros', 'CentrosController', 'centros');

// CONTRATOS
// View
Route::group(['middleware' => 'permission:contratos.view'], function () {
    Route::get('contratos', ['as' => 'contratos.index', 'uses' => 'Contratos\ContratosController@index']);
    Route::get('contratos/data', ['as' => 'contratos.rowsData', 'uses' => 'Contratos\ContratosController@rowsData']);
    Route::get('contratos/edit/{id}', ['as' => 'contratos.edit', 'uses' => 'Contratos\ContratosController@edit']);
    Route::get('contratos/tipos-documentos/data', ['as' => 'contratos.tiposDocumentosData', 'uses' => 'Contratos\ContratosController@tiposDocumentosData']);
    Route::get('contratos/documentacion/data', ['as' => 'contratos.documentacionData', 'uses' => 'Contratos\ContratosController@documentacionData']);
    Route::get('contratos/documentacion/faltante/data', ['as' => 'contratos.docFaltanteData', 'uses' => 'Contratos\ContratosController@docFaltanteData']);
    Route::get('contratos/contratistas/data', ['as' => 'contratos.contratistasData', 'uses' => 'Contratos\ContratosController@contratistasData']);
    Route::get('contratos/centros/data', ['as' => 'contratos.centrosData', 'uses' => 'Contratos\ContratosController@centrosData']);
    Route::get('contratos/edit/{contrato_id}/contratista/{contratista_id}', ['as' => 'contratos.contratista', 'uses' => 'Contratos\ContratosContratistaController@editContratista']);
    Route::get('contratos/edit/{contrato_id}/contratista/{contratista_id}/subcontratista/{subcontratista_id}', ['as' => 'contratos.subcontratista', 'uses' => 'Contratos\ContratosContratistaController@editSubcontratista']);
    Route::get('contratos/edit/{contrato_id}/trabajador/{trabajador_id}', ['as' => 'contratos.trabajador', 'uses' => 'Contratos\ContratosTrabajadorMaquinaController@editTrabajador']);
    Route::get('contratos/edit/{contrato_id}/maquina/{maquina_id}', ['as' => 'contratos.maquina', 'uses' => 'Contratos\ContratosTrabajadorMaquinaController@editMaquina']);
    Route::get('contratos/trabajadores/data', ['as' => 'contratos.trabajadoresData', 'uses' => 'Contratos\ContratosContratistaController@trabajadoresData']);
    Route::get('contratos/maquinas/data', ['as' => 'contratos.maquinasData', 'uses' => 'Contratos\ContratosContratistaController@maquinasData']);
});
// Create
Route::group(['middleware' => 'permission:contratos.create'], function () {
    Route::get('contratos/create', ['as' => 'contratos.create', 'uses' => 'Contratos\ContratosController@create']);
    Route::post('contratos', ['as' => 'contratos.store', 'uses' => 'Contratos\ContratosController@store']);
});
// Update
Route::group(['middleware' => 'permission:contratos.update'], function () {
    Route::match(['put', 'patch'], 'contratos/{id}', ['as' => 'contratos.update', 'uses' => 'Contratos\ContratosController@update']);
    Route::post('contratos/tipos-documentos/add', ['as' => 'contratos.addTiposDocumento', 'uses' => 'Contratos\ContratosController@addTiposDocumento']);
    Route::post('contratos/tipos-documentos/detach/{id}', ['as' => 'contratos.detachTipoDocumento', 'uses' => 'Contratos\ContratosController@detachDocumentoRequerido']);
    Route::post('contratos/tipos-documentos/obligatorio', ['as' => 'contratos.tipoDocumentoObligatorio', 'uses' => 'Contratos\ContratosController@cambiaTipoDocumentoObligatorio']);
    Route::post('contratos/centros/add', ['as' => 'contratos.addCentros', 'uses' => 'Contratos\ContratosController@addCentros']);
    Route::post('contratos/centros/detach/{id}', ['as' => 'contratos.detachCentros', 'uses' => 'Contratos\ContratosController@detachCentros']);
    // Doc. Empresa Principal
    Route::post('contratos/documentos-privados/add', ['as' => 'contratos.addDocumentoPrivado', 'uses' => 'Contratos\ContratosController@addDocumentoPrivado']);
    Route::get('contratos/documentos-privados/download/{idx}', ['as' => 'contratos.getDocumentoPrivado', 'uses' => 'Contratos\ContratosController@getDocumentoPrivado']);
    Route::post('contratos/documentos/add', ['as' => 'contratos.addDocumento', 'uses' => 'Contratos\ContratosController@addDocumento']);
    Route::get('contratos/documentos/{id}/data', ['as' => 'contratos.getDocumentoData', 'uses' => 'Contratos\ContratosController@getDocumentoData']);
});
// Update + Participar CAE
Route::group(['middleware' => 'canAtLeast:contratos.externo,contratos.update'], function () {
    Route::post('contratos/documentacion/add', ['as' => 'contratos.addDocumentacion', 'uses' => 'Contratos\ContratosController@addDocumentacion']);
    Route::post('contratos/documentacion/detach/{id}', ['as' => 'contratos.detachDocumentacion', 'uses' => 'Contratos\ContratosController@detachDocumentacion']);
    Route::get('contratos/documentacion/list', ['as' => 'contratos.listaDocumentacion', 'uses' => 'Contratos\ContratosController@listaDocumentacion']);
    Route::get('contratos/contratistas/list', ['as' => 'contratos.contratistasLista', 'uses' => 'Contratos\ContratosController@contratistasLista']);
    Route::post('contratos/contratistas/add', ['as' => 'contratos.addContratista', 'uses' => 'Contratos\ContratosController@addContratista']);
    Route::post('contratos/contratistas/detach/{id}', ['as' => 'contratos.detachContratistas', 'uses' => 'Contratos\ContratosController@detachContratistas']);
});
// Participar CAE
Route::group(['middleware' => 'canAtLeast:contratos.externo'], function () {
    Route::post('contratos/trabajadores-maquinas/add', ['as' => 'contratos.addTrabajadoresMaquinas', 'uses' => 'Contratos\ContratosContratistaController@addTrabajadoresMaquinas']);
    Route::post('contratos/trabajador-maquina/add', ['as' => 'contratos.addTrabajadorMaquina', 'uses' => 'Contratos\ContratosTrabajadorMaquinaController@addTrabajadoresMaquinas']);
    Route::post('contratos/trabajadores/detach/{trabajador_id}/{centro_id?}/{fecha_inicio_trabajos?}', ['as' => 'contratos.detachTrabajador', 'uses' => 'Contratos\ContratosContratistaController@detachTrabajador']);
    Route::post('contratos/maquinas/detach/{maquina_id}/{centro_id?}/{fecha_inicio_trabajos?}', ['as' => 'contratos.detachMaquina', 'uses' => 'Contratos\ContratosContratistaController@detachMaquina']);
    // Doc. Contratista/Subcontratista
    Route::post('contratos/edit/{contrato_id}/contratista/{contratista_id}/documentos/add', ['as' => 'contratos.addDocumentoContratista', 'uses' => 'Contratos\ContratosContratistaController@addDocumentoContratista']);
    Route::post('contratos/edit/{contrato_id}/contratista/{contratista_id}/subcontratista/{subcontratista_id}/documentos/add', ['as' => 'contratos.addDocumentoSubcontratista', 'uses' => 'Contratos\ContratosContratistaController@addDocumentoSubcontratista']);
    Route::get('contratos/edit/{contrato_id}/contratista/{contratista_id}/documentos/{documento_id}/data', ['as' => 'contratos.getDocumentoDataContratista', 'uses' => 'Contratos\ContratosContratistaController@getDocumentoDataContratista']);
    Route::get('contratos/edit/{contrato_id}/contratista/{contratista_id}/subcontratista/{subcontratista_id/documentos/{documento_id}/data', ['as' => 'contratos.getDocumentoDataSubcontratista', 'uses' => 'Contratos\ContratosContratistaController@getDocumentoDataSubcontratista']);
    // Doc. Trabajador
    Route::post('contratos/edit/{contrato_id}/trabajador/{trabajador_id}/documentos/add', ['as' => 'contratos.addDocumentoTrabajador', 'uses' => 'Contratos\ContratosTrabajadorMaquinaController@addDocumentoTrabajador']);
    Route::get('contratos/edit/{contrato_id}/trabajador/{trabajador_id}/documentos/{documento_id}/data', ['as' => 'contratos.getDocumentoDataTrabajador', 'uses' => 'Contratos\ContratosTrabajadorMaquinaController@getDocumentoDataTrabajador']);
    // Doc. Máquina
    Route::post('contratos/edit/{contrato_id}/maquina/{maquina_id}/documentos/add', ['as' => 'contratos.addDocumentoMaquina', 'uses' => 'Contratos\ContratosTrabajadorMaquinaController@addDocumentoMaquina']);
    Route::get('contratos/edit/{contrato_id}/maquina/{maquina_id}/documentos/{documento_id}/data', ['as' => 'contratos.getDocumentoDataMaquina', 'uses' => 'Contratos\ContratosTrabajadorMaquinaController@getDocumentoDataMaquina']);

});
// Permisos de acceso
Route::group(['middleware' => 'permission:acceso.update'], function () {
    Route::post('contratos/trabajadores-maquinas/access', 'Contratos\ContratosContratistaController@updateAcceso');
    Route::post('contratos/trabajador-maquina/access', 'Contratos\ContratosTrabajadorMaquinaController@updateAcceso');
});

// Remove
Route::post('contratos/remove/{id}', ['as' => 'contratos.remove', 'uses' => 'Contratos\ContratosController@remove'])->middleware('permission:contratos.delete');

// Route::get('test', function()
// {
//     $contrato = App\Models\Contrato::findOrFail(2);
//     dd($contrato->statusDocTrabajadoresContratista(1));
// });

// CONTROL ACCESO
Route::post('control-acceso/check', ['as' => 'compruebaAcceso', 'uses' => 'ControlAccesoController@compruebaAcceso'])->middleware('permission:acceso.view');
Route::get('control-acceso', ['as' => 'control-acceso', 'uses' => 'ControlAccesoController@index'])->middleware('permission:acceso.view');

// CONFIG
Route::get('config', ['as' => 'config', 'uses' => 'ConfigController@editConfig'])->middleware('permission:configs.view');
Route::match(['put', 'patch'], 'config', ['as' => 'config.save', 'uses' => 'ConfigController@saveConfig'])->middleware('permission:configs.update');

// SOPORTE
Route::post('soporte/enviar', ['as' => 'soporte.enviar', 'uses' => 'SoporteController@enviarMensaje']);
Route::get('soporte', ['as' => 'soporte.index', 'uses' => 'SoporteController@index']);
