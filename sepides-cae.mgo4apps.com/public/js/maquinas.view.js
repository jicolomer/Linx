$(function() {

    "use strict";

    var $anio = $('#anio_fabricacion');
    var $text = $('#anio_fabricacion_text');

    var anio = 0;

    function checkAnioFabricacion() {
        var a = $anio.val();
        if (a != anio) {
            var text = "";
            anio = a;
            if (anio > 1994) {
                text = "Debe adjuntar un documento de tipo 'Marcado CE'";
            } else {
                text = "Debe adjuntar un documento de tipo 'Certificado de conformidad RD 1217/95'";
            }
            $text.text(text);
        }
    }

    $anio.on('change', function(e) {
        checkAnioFabricacion();
    });

    checkAnioFabricacion();
});
