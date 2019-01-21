<?php
use Illuminate\Database\Seeder;

use Faker\Factory as Faker;
use Jenssegers\Date\Date;

use App\User;

class UsersSeeder extends Seeder
{
    public function run()
    {
        // Administrador
        $user = User::create([
            'nombre' => 'Administrador',
            'email' => 'administrador@cae.es',
            'telefono' => '666111222',
            'password' => bcrypt('3333'),
        ]);
        $user->attachRoleBySlug('administrador');
        $user->save();

        if (in_array(config('cae.empresa_cae'), [ 'SEPIDES' ])) {
            return;
        }

        // Control de accesos
        $user = User::create([
            'nombre' => 'Vigilante Seguridad',
            'email' => 'controlacceso@cae.es',
            'telefono' => '600999888',
            'password' => bcrypt('1111'),
        ]);
        $user->attachRoleBySlug('control');
        $user->save();
    }
}
