<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        Model::unguard();

        $this->call(ConfigSeeder::class);
        $this->call(ProvinciasSeeder::class);
        $this->call(CodigosCNAESeeder::class);
        $this->call(RolesSeeder::class);
        $this->call(PermissionsSeeder::class);
        $this->call(UsersSeeder::class);
        $this->call(TiposDocumentosSeeder::class);
        $this->call(TiposContratosSeeder::class);
        $this->call(TiposMaquinasSeeder::class);
        $this->call(EmpresasSeeder::class);
        $this->call(CentrosSeeder::class);
        $this->call(TrabajadoresSeeder::class);
        $this->call(ContratosSeeder::class);

        Model::reguard();
    }
}
