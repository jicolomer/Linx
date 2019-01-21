<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DemoSeeder extends Seeder
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
        $this->call(EmpresasSeeder::class);

        Model::reguard();
    }
}
