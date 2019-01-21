<?php
use Illuminate\Database\Seeder;

use Jenssegers\Date\Date;

use App\Models\TipoMaquina;

class TiposMaquinasSeeder extends Seeder
{
    public function run()
    {
        if (in_array(config('cae.empresa_cae'), [ 'SEPIDES' ])) {
            return;
        }

        $tipo = TipoMaquina::create([
            'nombre' => 'Vehículo',
            'notas' => ''
        ]);
        $tipo->tipos_documentos()->attach(11, ['obligatorio' => false]);
        $tipo->tipos_documentos()->attach(12, ['obligatorio'=> false]);

        $tipo = TipoMaquina::create([
            'nombre' => 'Grúa',
            'notas' => ''
        ]);
        $tipo->tipos_documentos()->attach(11, ['obligatorio' => false]);
        $tipo->tipos_documentos()->attach(12, ['obligatorio'=> false]);

        $tipo = TipoMaquina::create([
            'nombre' => 'Elevador',
            'notas' => ''
        ]);
        $tipo->tipos_documentos()->attach(11, ['obligatorio' => false]);
        $tipo->tipos_documentos()->attach(12, ['obligatorio'=> false]);

        // MAQUINAS
        DB::table('maquinas')->insert([
            'empresa_id' => 1,
            'tipo_maquina_id' => 1,
            'nombre' => 'Camión Mercedes',
            'marca' => 'Mercedes',
            'modelo' => '',
            'matricula' => '7860BDF',
            'anio_fabricacion' => 1992,
            'notas' => '',
            'fecha_alta' => Date::now()
        ]);
    }
}
