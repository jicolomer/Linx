<?php
namespace App\Http\Controllers\Base;

use Route;

class BaseRouter
{
    public static function basicRoutes($base_url, $controller_name, $permission_resource = null)
    {
        if ($permission_resource != null) {
            // No se protege porque a veces  se usa desde otros controllers
            Route::get($base_url . '/data', ['as' => $base_url . '.rowsData', 'uses' => $controller_name . '@rowsData']);
            // Protegidas
            Route::get($base_url, ['as' => $base_url . '.index', 'uses' => $controller_name . '@index'])
                ->middleware(self::permissionMiddleware($permission_resource, 'view'));
            Route::get($base_url . '/create', ['as' => $base_url . '.create', 'uses' => $controller_name . '@create'])
                ->middleware(self::permissionMiddleware($permission_resource, 'create'));
            Route::post($base_url, ['as' => $base_url . '.store', 'uses' => $controller_name . '@store'])
                ->middleware(self::permissionMiddleware($permission_resource, 'create'));
            Route::get($base_url . '/edit/{id}', ['as' => $base_url . '.edit', 'uses' => $controller_name . '@edit'])
                ->middleware(self::permissionMiddleware($permission_resource, 'update'));
            Route::match(['put', 'patch'], $base_url . '/{id}', ['as' => $base_url . '.update', 'uses' => $controller_name . '@update'])
                ->middleware(self::permissionMiddleware($permission_resource, 'update'));
            Route::post($base_url . '/remove/{id}', ['as' => $base_url . '.remove', 'uses' => $controller_name . '@remove'])
                ->middleware(self::permissionMiddleware($permission_resource, 'delete'));
            Route::post($base_url . '/restore/{id}', ['as' => $base_url . '.restore', 'uses' => $controller_name . '@restore'])
                ->middleware(self::permissionMiddleware($permission_resource, 'delete'));
        } else {
            // Sin protección
            Route::get($base_url . '/data', ['as' => $base_url . '.rowsData', 'uses' => $controller_name . '@rowsData']);
            Route::get($base_url, ['as' => $base_url . '.index', 'uses' => $controller_name . '@index']);
            Route::get($base_url . '/create', ['as' => $base_url . '.create', 'uses' => $controller_name . '@create']);
            Route::post($base_url, ['as' => $base_url . '.store', 'uses' => $controller_name . '@store']);
            Route::get($base_url . '/edit/{id}', ['as' => $base_url . '.edit', 'uses' => $controller_name . '@edit']);
            Route::match(['put', 'patch'], $base_url . '/{id}', ['as' => $base_url . '.update', 'uses' => $controller_name . '@update']);
            Route::post($base_url . '/remove/{id}', ['as' => $base_url . '.remove', 'uses' => $controller_name . '@remove']);
            Route::post($base_url . '/restore/{id}', ['as' => $base_url . '.restore', 'uses' => $controller_name . '@restore']);
        }
    }

    private static function permissionMiddleware($permission_resource, $permission)
    {
        if (is_array($permission_resource)) {
            $s = 'canAtLeast:';
            foreach ($permission_resource as $resource) {
                $s .= $resource . '.' . $permission . ',';
            }
            return rtrim($s, ',');
        } else {
            return 'permission:' . $permission_resource . '.' . $permission;
        }
    }
}
