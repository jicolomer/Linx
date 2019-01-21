<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Response;

use App\Models\Documento;

class APIUtilsController extends Controller
{
    // Devuelve una lista de los documentos en /storage/app que no están asignados en la BD
    public function documentosZombi()
    {
        $html = "<h1>Documentos 'zombi'</h1>" .
                "<hr /><ul>";
        foreach(glob(storage_path('app/doc') . '/*.*') as $file) {
            $filename = basename($file);
            $doc = Documento::where('filename', '=', $filename)->first();
            if ($doc == null) {
                $html .= "<li>$filename &nbsp | <a href='/api/utils/documento/$filename' target='_blank'>Ver...</a> | <a href='/api/utils/documento/$filename/delete'>Eliminar!</a></li>";
            }
        }

        $html .= "</li>";

        return response($html);
    }

    // Muestra/descarga un documento por su nombre de fichero (con extensión)
    public function documentoMostrar($filename)
    {
        if (Storage::disk('doc')->exists($filename)) {
            if ($file = Storage::disk('doc')->get($filename)) {
                return Response::make($file, 200, [
                    'Content-Type' => Storage::disk('doc')->getMimeType($filename),
                    'Content-Disposition: inline'
                ]);
            }
        }
        return back();
    }

    // ELIMINA!! un fichero de documento zombi
    public function documentoEliminar($filename)
    {
        if (Storage::disk('doc')->exists($filename)) {
            Storage::disk('doc')->delete($filename);
        }

        return back();
    }

}
