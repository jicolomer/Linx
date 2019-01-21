<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

use App\Models\Config;


class ConfigController extends Base\BaseController
{
    public function __construct()
    {
        parent::__construct(Config::class, 'config');
    }

    // Configuración de la EMPRESA
    public function editConfig(Request $request)
    {
        return $this->edit($request, 1);
    }

    public function edit(Request $request, $id)
    {
        $return_to = 'null';
        $filas_tablas = [ 10 => 10, 25 => 25, 50 => 50, 100 => 100 ];

        return parent::__edit($request, $id, compact('return_to', 'filas_tablas'));
    }

    public function saveConfig(Request $request)
    {
        $invitar_subcontratistas = $request->exists('invitar_subcontratistas');

        $fields = $request->except( ['invitar_subcontratistas', 'logo', 'logo_small' ]);
        $fields = array_merge($fields, compact('invitar_subcontratistas'));

        // Error de validación
        $validator = $this->validator($fields);
        if ($validator->fails()) {
            return redirect()->back()
                        ->with('errors', $validator->errors())
                        ->withInput();
        }

        $config = Config::findOrFail(1);
        $config->fill($fields)->save();

        // Actualizar la config
        Config::loadConfig();

        // Subir los logos
        if ($request->hasFile('logo')) {
            $this->save_logo($request->file('logo'));
        }
        if ($request->hasFile('logo_small')) {
            $this->save_logo($request->file('logo_small'), true);
        }

        return parent::__update_return();
    }

    // Save the logo file
    private function save_logo($file, $small = false) {
        $filename = 'logo-empresa' . ($small ? '-small' : '') . '.png';
        $file->storeAs('', $filename, 'public_img');
    }

    protected function validator(array $data)
    {
        $validator = Validator::make($data, [
            'nombre_corto' => 'required|max:10',
            'logo' => 'mimes:png|image',
            'logo_small' => 'mimes:png|image',
            'mimes_permitidos' => 'required|max:255',
            'caducidad_m_dias' => 'required|integer|min:0|max:30',
            'caducidad_t_dias' => 'required|integer|min:0|max:90',
            'caducidad_s_dias' => 'required|integer|min:0|max:180',
            'caducidad_a_dias' => 'required|integer|min:0|max:365',
            'caducidad_v_dias' => 'required|integer|min:0|max:365',
            'filas_tablas' => 'required',
            'filas_tablas_modal' => 'required',
        ]);

        $fields_names = [
            'nombre_corto' => 'Nombre Corto',
            'logo' => 'Logo Mediano',
            'logo_small' => 'Logo Pequeño',
            'mimes_permitidos' => 'Tipos Permitidos',
            'caducidad_m_dias' => 'Aviso Caducidad Mensual',
            'caducidad_t_dias' => 'Aviso Caducidad Trimestral',
            'caducidad_s_dias' => 'Aviso Caducidad Semestral',
            'caducidad_a_dias' => 'Aviso Caducidad Anual',
            'caducidad_v_dias' => 'Aviso Caducidad a Vencimiento',
            'filas_tablas' => 'Filas Tablas',
            'filas_tablas_modal' => 'Filas Tablas Modales',
        ];
        $validator->setAttributeNames($fields_names);

        return $validator;
    }
}
