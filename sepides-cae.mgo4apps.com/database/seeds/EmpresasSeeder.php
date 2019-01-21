<?php

use Illuminate\Database\Seeder;

use Faker\Factory as Faker;
use Jenssegers\Date\Date;

class EmpresasSeeder extends Seeder
{
    public function run()
    {
        // *********************************************************************
        // DEMO
        // *********************************************************************
        if (config('cae.empresa_cae') === 'DEMO') {
            DB::table('empresas')->insert([
                'id' => 0,
                'razon_social' => 'Empresa Demo, SA',
                // 'cif' => '',
                'direccion' => 'Av. Gran Vía, 1',
                'codigo_postal' => '28001',
                'municipio' => 'Madrid',
                'provincia_id' => 28,
                'telefono' => '910001000',
                'fax' => '',
                'created_at' => Date::now(),
                'updated_at' => Date::now()
            ]);
        }

        // *********************************************************************
        // CABB
        // *********************************************************************
        if (config('cae.empresa_cae') === 'CABB') {
            DB::table('empresas')->insert([
                'id' => 0,
                'razon_social' => 'Consorcio de Aguas Bilbao Bizkaia',
                'direccion' => 'C/ San Vicente nº 8, Edificio Albia 1, 4ª planta',
                'codigo_postal' => '48001',
                'municipio' => 'Bilbao',
                'provincia_id' => 48,
                'telefono' => '944873100',
                'fax' => '944873110',
                'created_at' => Date::now(),
                'updated_at' => Date::now()
            ]);
        }

        // *********************************************************************
        // SEPIDES
        // *********************************************************************
        if (config('cae.empresa_cae') === 'SEPIDES') {
            DB::table('empresas')->insert([
                'id' => 0,
                'razon_social' => 'SEPI Desarrollo Empresarial, S.A.',
                'cif' => 'A48001382',
                'direccion' => 'C/ Velázquez 134 Bis',
                'codigo_postal' => '28006',
                'municipio' => 'Madrid',
                'provincia_id' => 28,
                'telefono' => '913961147',
                // 'fax' => '944873110',
                'created_at' => Date::now(),
                'updated_at' => Date::now()
            ]);
        }

        DB::unprepared("UPDATE empresas SET id=0");

        if (config('cae.empresa_cae') === 'SEPIDES') {
            return;
        }

        $faker = Faker::create('es_ES');
        $id = 1;
        foreach (range(1, 30) as $index) {
            $tel = $faker->optional($weight = 0.5)->phoneNumber;
            if ($tel) {
                $tel = str_replace("+34", "", str_replace(" ", "", str_replace("-", "", $faker->phoneNumber)));
            } else {
                $tel = '';
            }
            $fax = $faker->optional($weight = 0.4)->numerify('9########');
            if (! $fax) {
                $fax = '';
            }
            $cif = $faker->optional($weight = 0.9)->randomElement(['A', 'B', 'G']);
            if ($cif) {
                $rs = $faker->company . ', ' . ($cif == 'A' ? 'S.A.' : ($cif == 'B' ? 'S.L.' : 'C.B.'));
                $cif .= $faker->numerify('########');
            } else {
                $cif = $faker->dni;
                $rs = $faker->firstName. ' ' . $faker->lastName . ' ' . $faker->lastName;
            }
            DB::table('empresas')->insert([
                'id' => $id,
                'razon_social' => $rs,
                'cif' => $cif,
                'direccion' => $faker->streetAddress,
                'codigo_postal' => $faker->postCode,
                'municipio' => $faker->city,
                'provincia_id' => $faker->numberBetween($min = 1, $max = 52),
                'telefono' => $faker->numerify('9########'),
                'telefono2' => $tel,
                'fax' => $fax,
                'codigo_cnae' => '',
                'created_at' => Date::now(),
                'updated_at' => Date::now()
            ]);

            $id++;
        }
    }
}
