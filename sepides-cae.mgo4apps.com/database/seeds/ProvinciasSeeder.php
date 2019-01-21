<?php

use Illuminate\Database\Seeder;

class ProvinciasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('provincias')->delete();

        DB::table('provincias')->insert([
            ['id' => '1', 'nombre' => 'Álava'],
            ['id' => '2', 'nombre' => 'Albacete'],
            ['id' => '3', 'nombre' => 'Alicante'],
            ['id' => '4', 'nombre' => 'Almería'],
            ['id' => '5', 'nombre' => 'Ávila'],
            ['id' => '6', 'nombre' => 'Badajoz'],
            ['id' => '7', 'nombre' => 'Baleares (Palma de Mallorca)'],
            ['id' => '8', 'nombre' => 'Barcelona'],
            ['id' => '9', 'nombre' => 'Burgos'],
            ['id' => '10', 'nombre' => 'Cáceres'],
            ['id' => '11', 'nombre' => 'Cádiz'],
            ['id' => '12', 'nombre' => 'Castellón'],
            ['id' => '13', 'nombre' => 'Ciudad Real'],
            ['id' => '14', 'nombre' => 'Córdoba'],
            ['id' => '15', 'nombre' => 'Coruña'],
            ['id' => '16', 'nombre' => 'Cuenca'],
            ['id' => '17', 'nombre' => 'Girona'],
            ['id' => '18', 'nombre' => 'Granada'],
            ['id' => '19', 'nombre' => 'Guadalajara'],
            ['id' => '20', 'nombre' => 'Guipúzcoa'],
            ['id' => '21', 'nombre' => 'Huelva'],
            ['id' => '22', 'nombre' => 'Huesca'],
            ['id' => '23', 'nombre' => 'Jaén'],
            ['id' => '24', 'nombre' => 'León'],
            ['id' => '25', 'nombre' => 'Lleida'],
            ['id' => '26', 'nombre' => 'La Rioja'],
            ['id' => '27', 'nombre' => 'Lugo'],
            ['id' => '28', 'nombre' => 'Madrid'],
            ['id' => '29', 'nombre' => 'Málaga'],
            ['id' => '30', 'nombre' => 'Murcia'],
            ['id' => '31', 'nombre' => 'Navarra'],
            ['id' => '32', 'nombre' => 'Ourense'],
            ['id' => '33', 'nombre' => 'Asturias'],
            ['id' => '34', 'nombre' => 'Palencia'],
            ['id' => '35', 'nombre' => 'Las Palmas'],
            ['id' => '36', 'nombre' => 'Pontevedra'],
            ['id' => '37', 'nombre' => 'Salamanca'],
            ['id' => '38', 'nombre' => 'Santa Cruz de Tenerife'],
            ['id' => '39', 'nombre' => 'Cantabria'],
            ['id' => '40', 'nombre' => 'Segovia'],
            ['id' => '41', 'nombre' => 'Sevilla'],
            ['id' => '42', 'nombre' => 'Soria'],
            ['id' => '43', 'nombre' => 'Tarragona'],
            ['id' => '44', 'nombre' => 'Teruel'],
            ['id' => '45', 'nombre' => 'Toledo'],
            ['id' => '46', 'nombre' => 'Valencia'],
            ['id' => '47', 'nombre' => 'Valladolid'],
            ['id' => '48', 'nombre' => 'Vizcaya'],
            ['id' => '49', 'nombre' => 'Zamora'],
            ['id' => '50', 'nombre' => 'Zaragoza'],
            ['id' => '51', 'nombre' => 'Ceuta'],
            ['id' => '52', 'nombre' => 'Melilla']
        ]);
    }
}
