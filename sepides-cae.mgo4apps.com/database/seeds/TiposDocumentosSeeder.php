<?php

use Illuminate\Database\Seeder;

use App\Models\TipoDocumento;

class TiposDocumentosSeeder extends Seeder
{
    public function run()
    {
        if (in_array(config('cae.empresa_cae'), [ 'SEPIDES' ])) {
            return;
        }

        // 1
        $tipo = TipoDocumento::create([
            'nombre' => 'Evaluación de Riesgos de Condiciones de Trabajo',
            'referencia' => '',
            'notas' => '',
            'ambito' => 'CEN',
            'tipo_caducidad' => 'A'
        ]);

        // 2
        $tipo = TipoDocumento::create([
            'nombre' => 'Plan de Emergencias o Medidas de actuación ante Emergencias',
            'referencia' => '',
            'notas' => 'Plan de Autoprotección o, en su defecto, las consignas a seguir en caso de emergencia en el centro en el que se desarrollen los trabajos.',
            'ambito' => 'CEN',
            'tipo_caducidad' => 'A'
        ]);

        // 3
        $tipo = TipoDocumento::create([
            'nombre' => 'Instrucciones de obligado cumplimiento para los contratistas',
            'referencia' => '',
            'notas' => 'Para trabajos especiales por la zona de trabajo o por la especial peligrosidad de los mismos.',
            'ambito' => 'EMP',
            'tipo_caducidad' => 'A'
        ]);

        // 4
        $tipo = TipoDocumento::create([
            'nombre' => 'Recibí de entrega de documentación (empresa principal)',
            'referencia' => 'F(PS-SST-10COOR)01',
            'notas' => 'Recibí de entrega de documentación por parte de la empresa principal al contratista. El contratista debe cumplimentarlo y subirlo para que sea verificado por la empresa principal.',
            'ambito' => 'CTA',
            'tipo_caducidad' => 'N',
        ]);

        // 5
        $tipo = TipoDocumento::create([
            'nombre' => 'Plan de prevención',
            'referencia' => '',
            'notas' => 'Plan de prevención de la empresa adjudicataria.',
            'ambito' => 'CTA',
            'tipo_caducidad' => 'N'
        ]);

        // 6
        $tipo = TipoDocumento::create([
            'nombre' => 'Evaluación de Riesgos Laborales de los trabajos a realizar',
            'referencia' => '',
            'notas' => 'Por cada actividad de trabajo que vaya a ejecutar la empresa se deberá adjuntar la evaluación de los riesgos laborales de dicha actividad, el lugar donde se va a realizar, la relación de trabajadores que intervendrán y las condiciones especiales (si las hay).',
            'ambito' => 'CTA',
            'tipo_caducidad' => 'N'
        ]);

        // 7
        $tipo = TipoDocumento::create([
            'nombre' => 'Modalidad de organización preventiva',
            'referencia' => 'F(PS-SST-10COOR)02',
            'notas' => 'Certificado (o contrato) de la modalidad preventiva adoptada por la empresa contratista.',
            'ambito' => 'CTA',
            'tipo_caducidad' => 'N'
        ]);

        // 8
        $tipo = TipoDocumento::create([
            'nombre' => 'Registro de subcontratación',
            'referencia' => 'F(PS-SST-10COOR)03',
            'notas' => 'Relación de los trabajadores de las empresas subcontratistas y trabajadores autónomos que intervienen en los trabajos.',
            'ambito' => 'CTA',
            'tipo_caducidad' => 'N'
        ]);

        // 9
        $tipo = TipoDocumento::create([
            'nombre' => 'Designación de Responsable Seguridad y/o Recurso Preventivo',
            'referencia' => 'F(PS-SST-10COOR)05',
            'notas' => 'Nombramiento del responsable de seguridad y/o Designación del Recurso Preventivo (si es otra persona).',
            'ambito' => 'CTA',
            'tipo_caducidad' => 'N'
        ]);

        // 10
        $tipo = TipoDocumento::create([
            'nombre' => 'Registro de Equipos de Trabajo',
            'referencia' => '',
            'notas' => 'Incluir: listado de equipos a utilizar, permisos de Industria o Certificados necesarios (Certificado de conformidad CE, Certificado de OCA, ENICRE, etc.)',
            'ambito' => 'CTA',
            'tipo_caducidad' => 'N'
        ]);

        // 11
        $tipo = TipoDocumento::create([
            'nombre' => 'Marcado CE',
            'referencia' => '',
            'notas' => 'Marcado CE para maquinaria fabricada con posterioridad al 1/01/1995',
            'ambito' => 'MAQ',
            'tipo_caducidad' => 'N'
        ]);
        $tipo->tag('maquinaria');

        // 12
        $tipo = TipoDocumento::create([
            'nombre' => 'Certificado de conformidad RD 1215/97',
            'referencia' => '',
            'notas' => 'Certificado de conformidad según el RD 1215/97 para maquinaria fabricada antes del 1/01/1995',
            'ambito' => 'MAQ',
            'tipo_caducidad' => 'N'
        ]);
        $tipo->tag('maquinaria');

        // 13
        $tipo = TipoDocumento::create([
            'nombre' => 'Certificado formación para trabajo en alturas',
            'referencia' => '',
            'notas' => '',
            'ambito' => 'TRA',
            'tipo_caducidad' => 'A'
        ]);
        $tipo->tag('formación');

        // 14
        $tipo = TipoDocumento::create([
            'nombre' => 'Certificado formación riesgos eléctricos',
            'referencia' => '',
            'notas' => '',
            'ambito' => 'TRA',
            'tipo_caducidad' => 'N'
        ]);
        $tipo->tag('formación');

        // 15
        $tipo = TipoDocumento::create([
            'nombre' => 'Carnet conducir B',
            'referencia' => '',
            'notas' => '',
            'ambito' => 'TRA',
            'tipo_caducidad' => 'V'
        ]);
        $tipo->tag('vehículos');

        // 16
        $tipo = TipoDocumento::create([
            'nombre' => 'Carnet conducir C+E',
            'referencia' => '',
            'notas' => 'Permiso C+E para conducir camión con remolque (carnet de trailer)',
            'ambito' => 'TRA',
            'tipo_caducidad' => 'V'
        ]);
        $tipo->tag('vehículos');
    }
}
