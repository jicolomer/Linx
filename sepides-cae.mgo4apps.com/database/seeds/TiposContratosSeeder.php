<?php

use Illuminate\Database\Seeder;

use App\Models\TipoContrato;
use App\Models\TipoDocumento;

class TiposContratosSeeder extends Seeder
{
    public function run()
    {
        // *********************************************************************
        // DEMO
        // *********************************************************************
        if (in_array(config('cae.empresa_cae'), [ 'DEMO' ])) {
            $tipo = TipoContrato::create([
                'nombre' => 'Prestación de servicios en instalaciones',
                'notas' => 'Caso general. Contratos de servicios en las instalaciones de la empresa principal.'
            ]);
            $this->adjuntarDocumentacion($tipo);

            DB::table('tipos_contratos')->insert([
                'nombre' => 'Trabajos sin riesgos significativos',
                'notas' => 'Trabajos ejecutados por las empresas Contratistas que realizan pequeños trabajos o servicios sin riesgos significativos.'
            ]);

            DB::table('tipos_contratos')->insert([
                'nombre' => 'Trabajos de obras de construcción (1627/97)',
                'notas' => 'Obras promovidas por la empresa principal,'
            ]);
        }

        // *********************************************************************
        // CABB
        // *********************************************************************
        if (config('cae.empresa_cae') === 'CABB') {
            $tipo = TipoContrato::create([
                'nombre' => 'Prestación de servicios en instalaciones',
                'notas' => 'Caso general. Contratos de servicios en las instalaciones del CABB. (apartado 7 de PE-SST-10COOR REV 02)'
            ]);
            $this->adjuntarDocumentacion($tipo);

            DB::table('tipos_contratos')->insert([
                'nombre' => 'Trabajos sin riesgos significativos',
                'notas' => 'Trabajos ejecutados por las empresas Contratistas que realizan pequeños trabajos o servicios sin riesgos significativos. (apartado 11 de PE-SST-10COOR REV 02)'
            ]);

            DB::table('tipos_contratos')->insert([
                'nombre' => 'Trabajos de obras de construcción (1627/97)',
                'notas' => 'Obras promovidas por el CABB'
            ]);

            DB::table('tipos_contratos')->insert([
                'nombre' => 'Contratos menores (Art. 138 TRLCSP)',
                'notas' => ''
            ]);

            DB::table('tipos_contratos')->insert([
                'nombre' => 'Contratos negociados sin publicidad',
                'notas' => ''
            ]);

            DB::table('tipos_contratos')->insert([
                'nombre' => 'Empresas con Servidumbre / Afecciones',
                'notas' => 'Trabajos ejecutados por empresas con "Servidumbre" dentro de las instalaciones del CABB (apartado 13 de PE-SST-10COOR REV 02)'
            ]);

            DB::table('tipos_contratos')->insert([
                'nombre' => 'Empresas explotadoras instalaciones "llave en mano"',
                'notas' => 'Contratos de explotación y mantenimiento "llave en mano". (apartado 8 de PE-SST-10COOR REV 02)'
            ]);
        }
    }

    private function adjuntarDocumentacion($tipo)
    {
        $tipos_documentos = TipoDocumento::where('activo', true)->get();

        foreach ($tipos_documentos as $tipo_documento) {
            $tipo->tipos_documentos()->attach($tipo_documento->id, ['obligatorio' => true]);
        }
    }
}
