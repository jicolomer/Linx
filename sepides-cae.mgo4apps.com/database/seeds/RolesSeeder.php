<?php
use Illuminate\Database\Seeder;

use Yajra\Acl\Models\Role;

class RolesSeeder extends Seeder
{
    public function run()
    {
        Role::create([
            'name' => 'Administrador',
            'slug' => 'administrador',
            'description' => 'Tareas administrativas, Configuración, Solución de errores.',
            'system' => true,
        ]);
        Role::create([
            'name' => 'Responsable PRL',
            'slug' => 'responsable',
            'description' => 'Tareas PRL, Permisos de acceso.',
            'system' => true,
        ]);
        Role::create([
            'name' => 'Técnico PRL',
            'slug' => 'tecnico',
            'description' => 'Tareas PRL.',
            'system' => true,
        ]);
        Role::create([
            'name' => 'Control Accesos',
            'slug' => 'control',
            'description' => 'Control de Accesos.',
            'system' => true,
        ]);
        Role::create([
            'name' => 'Empresa externa',
            'slug' => 'externo',
            'description' => 'Usuarios de empresas externar: contratistas o subcontratistas.',
            'system' => true,
        ]);
    }
}
