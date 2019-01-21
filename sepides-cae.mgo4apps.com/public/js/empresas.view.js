$(function() {

    "use strict";

    // CNAE
    var $cnae = $('#codigo_cnae');
    var $incnae = $('#codigo_cnae_c');
    $cnae.on('select2:select', function(e) {
        $incnae.val(e.params.data.id);
    });
    $cnae.on('select2:unselect', function(e) {
        $incnae.val('');
    });
    // CONSTRUCCION
    var $cton = $('#construccion');
    $cton.on('ifToggled', function(e) {
        enableConstruccion(e.target.checked);
    });
    function enableConstruccion(enable) {
        if (! enable) {
            $('#actividad_construccion-group').addClass('disabled');
            $('#actividad_construccion').attr('disabled', 'disabled');
            $('#actividad_construccion').val(null).trigger('change');
            $('#plantilla_indefinida-group').addClass('disabled');
            $('#plantilla_indefinida').attr('disabled', 'disabled');
            $('#plantilla_indefinida').iCheck('uncheck');
            $('#rea-group').addClass('disabled');
            $('#rea').attr('disabled', 'disabled');
            $('#rea').val(null);
        } else {
            $('#actividad_construccion-group').removeClass('disabled');
            $('#actividad_construccion').removeAttr('disabled');
            $('#plantilla_indefinida-group').removeClass('disabled');
            $('#plantilla_indefinida').removeAttr('disabled');
            $('#rea-group').removeClass('disabled');
            $('#rea').removeAttr('disabled');
        }
    }
    enableConstruccion($cton.attr('checked')!=null);
    // AUTONOMO
    var $autm = $('#autonomo');
    $autm.on('ifToggled', function(e) {
        enableAutonomo(e.target.checked);
    });
    function enableAutonomo(enable) {
        if (! enable) {
            $('#trabajadores_a_cargo-group').addClass('disabled');
            $('#trabajadores_a_cargo').attr('disabled', 'disabled');
            $('#trabajadores_a_cargo').iCheck('uncheck');
        } else {
            $('#trabajadores_a_cargo-group').removeClass('disabled');
            $('#trabajadores_a_cargo').removeAttr('disabled');
        }
    }
    enableAutonomo($autm.attr('checked')!=null);
});
