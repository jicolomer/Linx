<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

use Faker\Factory as Faker;
use Jenssegers\Date\Date;

use Yajra\Acl\Models\Permission;
use Yajra\Acl\Models\Role;

use App\User;

class PermissionsSeeder extends Seeder
{
    public function run()
    {
        // PERMISOS
        Permission::where('system', true)->delete();
        $configVer = Permission::create([
            'name' => 'Ver Configuración',
            'slug' => 'configs.view',
            'system' => true,
            'resource' => 'Configuración',
        ])->id;
        $configModif = Permission::create([
            'name' => 'Modificar Configuración',
            'slug' => 'configs.update',
            'system' => true,
            'resource' => 'Configuración',
        ])->id;
        $usuarios = $this->createResource('Usuarios' , true);
        $usuariosGlobal = $this->createResource('Usuarios (global)' , true);
        $tiposDocumentos = $this->createResource('Tipos Documentos' , true);
        $tiposMaquinas = $this->createResource('Tipos Máquinas' , true);
        $tiposContratos = $this->createResource('Tipos Contratos' , true);
        $maquinas = $this->createResource('Máquinas' , true);
        $maquinasGlobal = $this->createResource('Máquinas (global)' , true);
        $trabajadores = $this->createResource('Trabajadores' , true);
        $trabajadoresGlobal = $this->createResource('Trabajadores (global)' , true);
        $empresas = $this->createResource('Empresas' , true);
        $empresasGlobal = $this->createResource('Empresas (global)' , true);
        $centros = $this->createResource('Centros' , true);
        $contratos = $this->createResource('Contratos' , true);
        $contratosAddContratistas = Permission::create([
            'name' => 'Añadir Contratistas al Contrato',
            'slug' => 'contratos.add-contratistas',
            'system' => true,
            'resource' => 'Contratos',
        ])->id;
        $contratosExterno = Permission::create([
            'name' => 'Participar en Contratos',
            'slug' => 'contratos.externo',
            'system' => true,
            'resource' => 'Contratos',
        ])->id;
        $docValidar = Permission::create([
            'name' => 'Validar Documentos',
            'slug' => 'documentos.validar',
            'system' => true,
            'resource' => 'Documentos',
        ])->id;
        $accesosVer = Permission::create([
            'name' => 'Ver Control Accesos',
            'slug' => 'acceso.view',
            'system' => true,
            'resource' => 'Accesos',
        ])->id;
        $accesosModif = Permission::create([
            'name' => 'Modificar Permisos Acceso',
            'slug' => 'acceso.update',
            'system' => true,
            'resource' => 'Accesos',
        ])->id;
        $panelVer = Permission::create([
            'name' => 'Ver Panel',
            'slug' => 'dashboard.view',
            'system' => true,
            'resource' => 'Panel',
        ])->id;
        $permisosVer = Permission::create([
            'name' => 'Ver Permisos Aplicación',
            'slug' => 'permisos.view',
            'system' => true,
            'resource' => 'Permisos',
        ])->id;
        $permisosModif = Permission::create([
            'name' => 'Modificar Permisos Aplicación',
            'slug' => 'permisos.update',
            'system' => true,
            'resource' => 'Permisos',
        ])->id;

        // ROLES
        $administrador = User::findRoleBySlug('administrador');
        $responsable = User::findRoleBySlug('responsable');
        $tecnico = User::findRoleBySlug('tecnico');
        $control = User::findRoleBySlug('control');
        $externo = User::findRoleBySlug('externo');

        // Asignar PERMISOS
        // TODOS los permisos
        $todos = Permission::pluck('id')->toArray();
        $administrador->syncPermissions($todos);
        $administrador->save();
        // TECNICO
        $permisosTecnicos = array_merge(
            $usuarios, $tiposDocumentos, $tiposMaquinas, $tiposContratos,
            $maquinas, $maquinasGlobal, $trabajadores, $trabajadoresGlobal,
            $empresas, $empresasGlobal, $centros, $contratos,
            [$contratosAddContratistas, $docValidar, $accesosVer, $panelVer]
        );
        $tecnico->syncPermissions($permisosTecnicos);
        $tecnico->save();
        // RESPONSABLE
        $responsable->syncPermissions(array_merge($permisosTecnicos, [$accesosModif]));
        $responsable->save();
        // CONTROL accessos
        $control->assignPermission([$accesosVer]);
        $control->save();
        // EMP. EXTERNAS
        $externo->syncPermissions(array_merge(
            $usuarios, $maquinas, $trabajadores, $empresas,
            [$contratos[0], $contratosExterno, $panelVer]
        ));
        $externo->save();

    }

    public function createResource($resource, $system = true)
    {
        $group        = ucfirst($resource);
        $slug         = $this->slugify($this->removeAccents($group));
        $permissions  = [
            [
                'slug'     => $slug . '.view',
                'resource' => $group,
                'name'     => 'Ver ' . $group,
                'system'   => $system,
            ],
            [
                'slug'     => $slug . '.create',
                'resource' => $group,
                'name'     => 'Crear ' . $group,
                'system'   => $system,
            ],
            [
                'slug'     => $slug . '.update',
                'resource' => $group,
                'name'     => 'Modificar ' . $group,
                'system'   => $system,
            ],
            [
                'slug'     => $slug . '.delete',
                'resource' => $group,
                'name'     => 'Eliminar ' . $group,
                'system'   => $system,
            ],
        ];

        $collection = new Collection;
        foreach ($permissions as $permission) {
            try {
                $collection->push(Permission::create($permission));
            } catch (\Exception $e) {
                // permission already exists.
            }
        }

        return $collection->pluck('id')->toArray();
    }

    private function slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);
        // trim
        $text = trim($text, '-');
        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);
        // lowercase
        $text = strtolower($text);
        if (empty($text)) {
            return 'n-a';
        }
        return $text;
    }

    private function removeAccents($text)
    {
        $unwanted_array = array(
            'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
            'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
            'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
            'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
            'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );

        return strtr($text, $unwanted_array);
    }

}
