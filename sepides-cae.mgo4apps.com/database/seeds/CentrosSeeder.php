<?php

use Illuminate\Database\Seeder;
use Jenssegers\Date\Date;

class CentrosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // *********************************************************************
        // DEMO
        // *********************************************************************
        if (in_array(config('cae.empresa_cae'), [ 'DEMO' ])) {
            DB::table('centros')->insert([
                'nombre' => 'Oficina Central',
                'direccion' => 'Dirección de la oficina central',
                'codigo_postal' => '28001',
                'municipio' => 'Madrid',
                'provincia_id' => 28,
                'telefono_centro' => '910001000',
                'fax_centro' => '',
                'email_centro' => 'oficina_central@cae.es',
                'persona_contacto' => '',
                'telefono_contacto' => '',
                'email_contacto' => '',
                'created_at' => Date::now(),
                'updated_at' => Date::now()
            ]);
            DB::table('centros')->insert([
                'nombre' => 'Fábrica de la empresa',
                'direccion' => 'Dirección de la fábrica de la empresa',
                'codigo_postal' => '28001',
                'municipio' => 'Madrid',
                'provincia_id' => 28,
                'telefono_centro' => '910001333',
                'fax_centro' => '',
                'email_centro' => 'fabrica_empresa@cae.es',
                'persona_contacto' => '',
                'telefono_contacto' => '',
                'email_contacto' => '',
                'created_at' => Date::now(),
                'updated_at' => Date::now()
            ]);
            DB::table('centros')->insert([
                'nombre' => 'Obra de la empresa',
                'direccion' => 'Dirección de la obra de la empresa',
                'codigo_postal' => '28001',
                'municipio' => 'Madrid',
                'provincia_id' => 28,
                'telefono_centro' => '910001999',
                'fax_centro' => '',
                'email_centro' => 'obra_empresa@cae.es',
                'persona_contacto' => '',
                'telefono_contacto' => '',
                'email_contacto' => '',
                'created_at' => Date::now(),
                'updated_at' => Date::now()
            ]);
        }

        // *********************************************************************
        // CABB
        // *********************************************************************
        if (config('cae.empresa_cae') === 'CABB') {
            DB::table('centros')->insert([
                'nombre' => 'E.D.A.R. Galindo',
                'direccion' => 'Direccion de EDAR Galindo',
                'codigo_postal' => '48001',
                'municipio' => 'Bilbao',
                'provincia_id' => 48,
                'telefono_centro' => '945678901',
                'fax_centro' => '945678909',
                'email_centro' => 'edar.galindo@cabb.es',
                'persona_contacto' => 'Antonio Pérez',
                'telefono_contacto' => '600999111',
                'email_contacto' => 'antonio.perez@cabb.es',
                'created_at' => Date::now(),
                'updated_at' => Date::now()
            ]);
            DB::table('centros')->insert([
                'nombre' => 'E.T.A.P. Venta Alta',
                'direccion' => 'Direccion de ETAP Venta Alta',
                'codigo_postal' => '48901',
                'municipio' => 'Barakaldo',
                'provincia_id' => 48,
                'telefono_centro' => '947766888',
                'fax_centro' => '',
                'email_centro' => 'etap.ventaalta@cabb.es',
                'persona_contacto' => 'José Navarro',
                'telefono_contacto' => '600555222',
                'email_contacto' => 'jose.navarro@cabb.es',
                'created_at' => Date::now(),
                'updated_at' => Date::now()
            ]);
            DB::table('centros')->insert([
                'nombre' => 'Oficinas Albia',
                'direccion' => 'Direccion de Oficina Albia',
                'codigo_postal' => '48382',
                'municipio' => 'Getxo',
                'provincia_id' => 48,
                'telefono_centro' => '945555000',
                'fax_centro' => '941234567',
                'email_centro' => 'albia.oficinas@cabb.es',
                'persona_contacto' => 'Asier Cobeaga',
                'telefono_contacto' => '618161616',
                'email_contacto' => 'asier.cobeaga@cabb.es',
                'created_at' => Date::now(),
                'updated_at' => Date::now()
            ]);
            DB::table('centros')->insert([
                'nombre' => 'Red de Colectores',
                'direccion' => '',
                'codigo_postal' => '',
                'municipio' => '',
                'provincia_id' => 48,
                'telefono_centro' => '',
                'fax_centro' => '',
                'email_centro' => 'red.colectores@cabb.es',
                'persona_contacto' => 'Ernesto Reverte',
                'telefono_contacto' => '606112233',
                'email_contacto' => 'ernesto.reverte@cabb.es',
                'created_at' => Date::now(),
                'updated_at' => Date::now()
            ]);
            DB::table('centros')->insert([
                'nombre' => 'Red Primaria de Abastecimiento',
                'direccion' => '',
                'codigo_postal' => '',
                'municipio' => '',
                'provincia_id' => 48,
                'telefono_centro' => '',
                'fax_centro' => '',
                'email_centro' => 'red.abastecimiento@cabb.es',
                'persona_contacto' => 'Juan José Mesa',
                'telefono_contacto' => '605152535',
                'email_contacto' => 'jjose.mesa@cabb.es',
                'created_at' => Date::now(),
                'updated_at' => Date::now()
            ]);
            DB::table('centros')->insert([
                'nombre' => 'Presa de Undurraga',
                'direccion' => 'Dirección Presa',
                'codigo_postal' => '48144',
                'municipio' => 'Undurraga',
                'provincia_id' => 48,
                'telefono_centro' => '941617189',
                'fax_centro' => '945678901',
                'email_centro' => 'presa.undurraga@cabb.es',
                'persona_contacto' => 'Marcos Uribe',
                'telefono_contacto' => '606122232',
                'email_contacto' => 'marcos.uribe@cabb.es',
                'created_at' => Date::now(),
                'updated_at' => Date::now()
            ]);
        }
    }
}
