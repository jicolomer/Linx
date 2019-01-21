(function($) {
    "use strict";

    $.DocumentosValidation = function() {
        var $validationDlg = $('#aprobar-documento-modal-dialog');
        var $validationForm = $('#aprobar-documento-modal-dialog-form');
        var $aprobarButton = $('#aprobar-documento-modal-aprobar-button');
        var $noAprobarButton = $('#aprobar-documento-modal-no-aprobar-button');

        function sendForm(datatable) {
            var formData = $validationForm.serialize();
            var url = '/api/documentos/valida-documento';
            $.post(url, formData, function(res) {
                if (res.result === 'success') {
                    $.App.notify.success(res.msg);
                    datatable.ajax.reload();
                } else {
                    $.App.notify.error(res.msg);
                }
            }).fail(function(res) {
                $.App.notify.error(res.status + ' - ' + res.statusText);
            }).always(function() {
                $validationDlg.modal('hide');
            });
        }

        function removeValidationStatusInput() {
            var $input = $('#val_status_validacion');
            if ($input != null) {
                $input.remove();
            }
        };

        function disableNoAprobarButton(disable) {
            if (disable) {
                $noAprobarButton.attr('disabled', 'disabled');
                $noAprobarButton.removeClass('btn-danger');
            } else {
                $noAprobarButton.removeAttr('disabled');
                $noAprobarButton.addClass('btn-danger');
            }
        }

        var output = {
            init: function() {
                $('#val_notas_validacion').on('input', function(e) {
                    var value = $('#val_notas_validacion').val();
                    disableNoAprobarButton(!value.trim());
                });
            },

            showValidationModal: function(documento_id, datatable) {
                $aprobarButton.show();
                $noAprobarButton.show();
                disableNoAprobarButton(true);
                var url = "/api/documentos/data/" + documento_id;
                $.getJSON(url, function(d) {
                    if (d && d.result == 'success') {
                        $validationForm[0].reset();
                        $('#val_id').val(d.data.id);
                        $('#val_version').val(d.data.version);
                        $('#val_tipo_documento').val(d.data.tipo_documento_nombre);
                        $('#val_nombre').val(d.data.nombre);
                        $('#val_fecha_documento').val(d.data.fecha_documento);
                        $('#val_fecha_caducidad').val(d.data.fecha_caducidad);
                        $('#val_notas').val(d.data.notas);
                        $('#val_filename').val(d.data.original_filename);
                        $('#aprobar-documento-modal-descargar-button').attr('href', '/api/documentos/download/' + d.data.id);
                        if (d.data.horas_formacion) {
                            $('#val_horas_formacion').val(d.data.horas_formacion);
                        } else {
                            $('#val_horas_formacion-group').hide();
                        }
                        if (d.data.status_validacion == -1) {
                            $noAprobarButton.hide();
                        } else if (d.data.status_validacion == 1) {
                            $aprobarButton.hide();
                        }
                        $validationDlg.modal('show');
                    } else {
                        $validationDlg.modal('hide');
                        if (d) {
                            $.App.notify.error(d.msg);
                        }
                    }
                });
                $aprobarButton.unbind('click');
                $aprobarButton.on('click', function(e) {
                    removeValidationStatusInput();
                    $validationForm.append('<input type="hidden" name="documento_aprobado" id="val_status_validacion" value="true" />');
                    sendForm(datatable);
                });
                $noAprobarButton.unbind('click');
                $noAprobarButton.on('click', function(e) {
                    removeValidationStatusInput();
                    $validationForm.append('<input type="hidden" name="documento_rechazado" id="val_status_validacion" value="true" />');
                    sendForm(datatable);
                });
            },
        }

        return output;
    }
})(window.jQuery);
