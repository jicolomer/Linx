<?php

use Illuminate\Database\Seeder;

class CodigosCNAESeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        function csv_to_array($filename='', $delimiter=',')
        {
            if(!file_exists($filename) || !is_readable($filename))
                return FALSE;

            $data = array();
            if (($handle = fopen($filename, 'r')) !== FALSE)
            {
                while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
                {
                    array_push($data, $row);
                }
                fclose($handle);
            }
            return $data;
        }

        DB::table('codigos_cnae')->delete();

        $csvFile = base_path().'/resources/data/cnae93.csv';

        $array = csv_to_array($csvFile, '|');
        foreach ($array as $row) {
            DB::table('codigos_cnae')->insert([
                'codigo' => $row[0],
                'descripcion' => $row[1],
            ]);
        }
    }
}
