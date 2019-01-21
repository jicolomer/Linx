<?php
namespace App\Http\Controllers\Documentos;

use Route;
use App\Http\Controllers\Base\BaseRouter;

class DocumentosRouter
{

    public static function documentos($base_url, $controller_name, $permission_resource = null)
    {
        Route::get($base_url . '/documentos/data', ['as' => $base_url . '.documentosData', 'uses' => $controller_name . '@documentosData']);
        Route::post($base_url . '/documentos/add', ['as' => $base_url . '.addDocumento', 'uses' => $controller_name . '@addDocumento']);
        Route::get($base_url . '/documentos/{id}/data', ['as' => $base_url . '.getDocumentoData', 'uses' => $controller_name . '@getDocumentoData']);
        Route::post($base_url . '/documentos/detach/{id}', ['as' => $base_url . '.detachDocumento', 'uses' => $controller_name . '@detachDocumento']);
        BaseRouter::basicRoutes($base_url, $controller_name, $permission_resource);
    }

    public static function tiposDocumentos($base_url, $controller_name, $permission_resource = null)
    {
        Route::get($base_url . '/tipos-documentos/data', ['as' => $base_url . '.tiposDocumentosData', 'uses' => $controller_name . '@tiposDocumentosData']);
        Route::post($base_url . '/tipos-documentos/add', ['as' => $base_url . '.addTiposDocumento', 'uses' => $controller_name . '@addTiposDocumento']);
        Route::post($base_url . '/tipos-documentos/detach/{id}', ['as' => $base_url . '.detachTipoDocumento', 'uses' => $controller_name . '@detachTipoDocumento']);
        Route::post($base_url . '/tipos-documentos/obligatorio', ['as' => $base_url . '.tipoDocumentoObligatorio', 'uses' => $controller_name . '@cambiaTipoDocumentoObligatorio']);
        BaseRouter::basicRoutes($base_url, $controller_name, $permission_resource);
    }

}
