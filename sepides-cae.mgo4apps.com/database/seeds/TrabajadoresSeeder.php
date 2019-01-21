<?php
use Illuminate\Database\Seeder;

use Faker\Factory as Faker;
use Jenssegers\Date\Date;

use App\Models\Trabajador;
use App\User;

class TrabajadoresSeeder extends Seeder
{
    public function run()
    {
        if (in_array(config('cae.empresa_cae'), [ 'SEPIDES' ])) {
            return;
        }

        $faker = Faker::create('es_ES');

        // ***************************************
        // EMPLEADOS EMPRESA PRINCIPAL + USUARIOS
        // ***************************************
        $this->empleado(
            $faker,
            0,
            null,
            null,
            'tecnicoprl@cae.es',
            'Tecnico PRL',
            false,
            false,
            'tecnico'
        );
        $this->empleado(
            $faker,
            0,
            null,
            null,
            'responsableprl@cae.es',
            'Responsable',
            false,
            false,
            'responsable'
        );

        // **********************************
        // EMPLEADOS CABB + USUARIOS
        // **********************************
        if (config('cae.empresa_cae') === 'CABB') {
            $this->empleado(
                $faker,
                0,
                'Zigor',
                'Otaola',
                'zigor.otaola@cae.es',
                'Tecnico PRL',
                false,
                false,
                'tecnico'
            );
            $this->empleado(
                $faker,
                0,
                'Maika',
                'Vila',
                'maika.vila@cae.es',
                'Tecnico PRL',
                false,
                false,
                'tecnico'
            );
            $this->empleado(
                $faker,
                0,
                'David',
                'Donaire',
                'david.donaire@cae.es',
                'Tecnico PRL',
                false,
                false,
                'tecnico'
            );
            $this->empleado(
                $faker,
                0,
                'Elena',
                'Llorente',
                'elena.llorente@cae.es',
                'Tecnico PRL',
                false,
                false,
                'tecnico'
            );
            $this->empleado(
                $faker,
                0,
                'Joseba',
                'Elorza',
                'joseba.elorza@cae.es',
                'Tecnico PRL',
                false,
                false,
                'tecnico'
            );
            $this->empleado(
                $faker,
                0,
                'Marta',
                'González',
                'marta.gonzalez@cae.es',
                'Tecnico PRL',
                false,
                false,
                'tecnico'
            );
            $this->empleado(
                $faker,
                0,
                'Anartz',
                'Fernández',
                'anartz.fernandez@cae.es',
                'Tecnico PRL',
                false,
                false,
                'tecnico'
            );
            $this->empleado(
                $faker,
                0,
                'Ion',
                'Alonso',
                'ion.alonso@cae.es',
                'Tecnico PRL',
                false,
                false,
                'tecnico'
            );
            $this->empleado(
                $faker,
                0,
                'Cristina',
                'Arrieta',
                'cristina.arrieta@cae.es',
                'Tecnico PRL',
                false,
                false,
                'tecnico'
            );
        }

        // **********************************
        // TRABAJADORES EXTERNOS - EMPRESA 1
        // **********************************
        $this->empleado(
            $faker,
            1,
            null,
            null,
            null,
            'Encargado',
            false,
            true,
            'externo'
        );
        $this->empleado(
            $faker,
            1,
            null,
            null,
            'contratista@cae.es',
            'Técnico PRL',
            true,
            false,
            'externo'
        );
        $this->empleado(
            $faker,
            1,
            null,
            null,
            null,
            'Electricista (Oficial 1ª)'
        );
        $this->empleado(
            $faker,
            1,
            null,
            null,
            null,
            'Soldador'
        );

        // **********************************
        // TRABAJADORES EXTERNOS - ALEATORIOS
        // **********************************
        for ($i=0; $i < 30; $i++) {
            $this->empleado($faker);
        }
    }

    private function empleado($faker, $empresa_id = -1, $nombre = null, $apellidos = null, $email = null, $puesto = null, $recurso = false, $delegado = false, $rol = null)
    {
        $t = new Trabajador();

        $t->empresa_id = ($empresa_id == -1) ? $faker->numberBetween($min = 2, $max = 30) : $empresa_id;
        $t->nombre = ($nombre == null) ? $faker->firstName() : $nombre;
        $t->apellidos = ($apellidos == null) ? $faker->lastName() : $apellidos;
        $t->email = ($email == null) ? $faker->email() : $email;
        $t->puesto = ($puesto == null) ? $this->puesto($faker) : $puesto;
        $t->recurso_preventivo = $recurso;
        $t->delegado_prevencion = $delegado;

        $t->nif = $this->dni($faker);
        $t->nss = $this->nss($faker);
        $t->fecha_nacimiento = $this->fecha_nacimiento($faker);
        $t->direccion = $faker->streetAddress;
        $t->codigo_postal = $faker->postCode;
        $t->municipio = $faker->city;
        $t->provincia_id = $this->provincia($faker);
        $t->telefono = $this->telefono($faker);
        $t->fecha_alta = $faker->dateTimeThisYear($max = 'now');

        $t->save();

        if ($rol != null) {
            // Crear usuario
            $user = new User();
            $user->nombre = $t->nombre . ' ' . $t->apellidos;
            $user->email = $t->email;
            $user->telefono = $t->telefono;
            $user->nif = $t->nif;
            $user->cargo = $t->puesto;
            $user->empresa_id = $t->empresa_id;
            $user->password = bcrypt('1111');
            $user->save();

            $user->attachRoleBySlug($rol);
            $user->save();

            $user->trabajador()->save($t);
        }

        return $t;
    }

    private function telefono($faker)
    {
        return str_replace("+34", "", str_replace(" ", "", str_replace("-", "", $faker->phoneNumber)));
    }

    private function fecha_nacimiento($faker)
    {
        return $faker->dateTimeBetween($startDate = '-60 years', $endDate = '-18 years', $timezone = date_default_timezone_get());
    }

    private function provincia($faker)
    {
        return $faker->numberBetween($min = 1, $max = 52);
    }

    private function nss($faker)
    {
        return sprintf('%08d', $faker->randomNumber(6)) . sprintf('%08d', $faker->randomNumber(6));
    }

    private function dni($faker)
    {
        return sprintf('%08d', $faker->randomNumber(8)) . strtoupper($faker->randomLetter());
    }

    private function puesto($faker)
    {
        $p = [ 'Pintor', 'Oficial 1ª', 'Soldador', 'Soldador (Oficial 1ª)',
               'Técnico Electricidad', 'Electricista', 'Peón Albañil',
               'Albañil (Oficial 1ª)', 'Director', 'Encargado', 'Encofrador',
               'Técnico Informático', 'Ingeniero', 'Arquitecto', 'Aparejador',
               'Perito', 'Gruista', 'Conductor', 'Maquinista', 'Montador'
        ];

        return $faker->randomElement($p);
    }
}
