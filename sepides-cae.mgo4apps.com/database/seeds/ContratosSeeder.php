<?php

use Illuminate\Database\Seeder;

use Jenssegers\Date\Date;
use Faker\Factory as Faker;

use App\Models\Contrato;
use App\Models\TipoContrato;

class ContratosSeeder extends Seeder
{
    public function run()
    {
        if (in_array(config('cae.empresa_cae'), [ 'SEPIDES' ])) {
            return;
        }

        $faker = Faker::create('es_ES');

        $contrato = new Contrato();
        $contrato->tipo_contrato_id = 1;
        $contrato->referencia = 'AF1234/16';
        $contrato->nombre = 'Reforma Oficinas de Dpto. Prevención';
        $contrato->importe_contrato = 25475.80;
        $contrato->responsable_contrato_id = 2;
        $contrato->tecnico_prl_id = 10;
        $contrato->coordinador_cap_id = 5;
        $contrato->fecha_firma = $faker->dateTimeBetween($startDate = '-6 months', $endDate = '-2 months', $timezone = date_default_timezone_get());
        $contrato->fecha_inicio_obras = $faker->dateTimeBetween($startDate = '-30 days', $endDate = '-10 days', $timezone = date_default_timezone_get());
        $contrato->fecha_fin_obras = $faker->dateTimeBetween($startDate = '+3 months', $endDate = '+6 months', $timezone = date_default_timezone_get());
        $contrato->save();

        $contrato->addDocumentacionRequerida();
    }
}
