<?php

use Illuminate\Database\Seeder;

use App\Models\Config;

class ConfigSeeder extends Seeder
{
    public function run()
    {
        $cfg = new Config();

        $cfg->nombre_corto = config('cae.empresa_cae');

        $cfg->save();
    }
}
