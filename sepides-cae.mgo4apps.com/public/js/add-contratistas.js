(function($) {
    "use strict";
    $.AddContratistas = function() {
        var _baseRoute = null;
        var _contratista = false;
        var _addSubcontratistaOf = null;
        var _typeStr = 'Contratista';

        var _listaUsuariosEmpresaRoute = '';

        var $modal = $('#add-contratista-wizard');
        var $form = $('#add-contratista-wizard-form');
        var $modalTitle = $('#add-contratista-wizard-title');
        var $errors = $('#add-contratista-wizard-errors-box');
        var $errors_ul = $('#add-contratista-wizard-errors-box ul');

        var $page1 = $('#add-contratista-wizard-page-1');
        var $page2a = $('#add-contratista-wizard-page-2a');
        var $page2b = $('#add-contratista-wizard-page-2b');

        var $nextBtn = $('#add-contratista-wizard-next-button');
        var $backBtn = $('#add-contratista-wizard-back-button');
        var $finalBtn = $('#add-contratista-wizard-final-button');

        var $newChk = $('#contratista_nuevo');
        var $conSelect = $('#contratista_seleccionado');
        var $personaSelect = $('#persona_contacto');

        function setInitialStatus() {
            resetErrors();
            $modalTitle.text('Añadir ' + _typeStr + ' - Paso 1/2');
            $("label[for='contratista_existente']").text(_typeStr + ' existente');
            $("label[for='contratista_nuevo']").text('Crear nuevo ' + _typeStr);
            $('#add-contratista-wizard-page-2a h5:first-child').text('Datos del nuevo ' + _typeStr.toLowerCase());
            $('#persona-contacto-group h5:first-child').text('Persona de contacto del ' + _typeStr.toLowerCase());
            $('#add-contratista-wizard-page-2b h5:first-child').text('Persona de contacto del ' + _typeStr.toLowerCase());
            $('#2a_li_2').html('... se asociará la <em>Empresa</em> creada como <strong>' + _typeStr + '</strong> del contrato.')
            $('#2a_li_3').html('... se creará un nuevo <strong>Trabajador</strong> para la <em>empresa ' + _typeStr.toLowerCase() + '</em> con los datos introducidos.')
            $('#2b_li_1').html('... se asociará la <em>Empresa</em> elegida como <strong>' + _typeStr + '</strong> del contrato.')
            $page1.show();
            $page2a.hide();
            $page2b.hide();
            $nextBtn.show();
            $backBtn.hide();
            $finalBtn.hide();
            checkOptionSelected();
        }

        function resetErrors() {
            $errors.hide();
            $errors_ul.empty();
            $form.find('.form-group').removeClass('has-error');
        }

        function resetConSelect(contratista) {
            if (contratista == null) {
                contratista = 0;
            }
            var url = _baseRoute + '/contratistas/list?c=' + contratista;
            $.getJSON(url, function(d) {
                if (d) {
                    $conSelect.empty();
                    $conSelect.select2({
                        data: d,
                        placeholder: 'Seleccione ' + _typeStr.toLowerCase() + '...',
                        width: '100%',
                        allowClear: true,
                        language: 'es',
                        dropdownParent: $modal,
                    });
                    $conSelect.val(null).trigger("change");
                }
            });
        }

        function resetCamposFormulario() {
            $form.find('input').each(function() {
                $(this).val('');
            });
        }

        function checkOptionSelected() {
            if ($newChk.is(':checked')) {
                enableNext(true);
                $conSelect.prop('disabled', true);
            } else {
                enableNext($conSelect.val() != '');
                $conSelect.prop('disabled', false);
            }
        }

        function enableNext(enable) {
            $nextBtn.prop('disabled', !enable);
            if (enable) {
                $nextBtn.addClass('btn-primary');
            } else {
                $nextBtn.removeClass('btn-primary');
            }
        }

        function resetPersonaSelect() {
            $personaSelect.empty();
            $personaSelect.select2({
                placeholder: 'Seleccione la persona de contacto...',
                width: '100%',
                allowClear: true,
                language: 'es',
                dropdownParent: $modal,
            });
        }

        function fillPersonaSelect(id) {
            var url = _listaUsuariosEmpresaRoute.replace('XX', id);
            $.getJSON(url, function(d) {
                if (d) {
                    $personaSelect.empty();
                    $personaSelect.select2({
                        data: d,
                        placeholder: 'Seleccione la persona de contacto...',
                        width: '100%',
                        allowClear: true,
                        language: 'es',
                        dropdownParent: $modal,
                    });
                    $personaSelect.val(null).trigger("change");
                }
            });
        }

        function finalButtonClicked(e) {
            modalLoading.init(true);
            var url = $form.attr('action');
            var formData = {};
            if ($newChk.is(':checked')) {
                formData['contratista_nuevo'] = true;
            }
            $form.find('input').each(function() {
                formData[ $(this).attr('name') ] = $(this).val();
            });
            $form.find('select').each(function() {
                formData[ $(this).attr('name') ] = $(this).val();
            });
            resetErrors();
            $.post(url, formData, function(response) {
                location.reload();
            }).fail(function(xhr, status, error) {
                modalLoading.init(0);
                console.log(xhr.responseText);
                var jsonResp = $.parseJSON(xhr.responseText)
                var errors = jsonResp['errors'];
                $.each(errors, function(index, value) {
                    $form.find('.form-group').each(function() {
                        var $field = $(this).find('#' + index);
                        if ($field.length) {
                            $(this).addClass('has-error');
                        }
                    });
                    $errors_ul.append('<li>' + value + '</li>');
                });
                $errors.show();
            });
        }

        function detachContratistaConfirmationDialog(subcontratista, yes_callback) {
            bootbox.dialog({
                title: "Por favor, confirme",
                message: "<h3>¿Está seguro de querer quitar el " + (subcontratista ? "Subc" : "C") + "ontratista del contrato?</h3>",
                className: "modal-danger",
                onEscape: function() {},
                buttons: {
                    si: {
                        label: "Sí, quiero quitarlo",
                        className: "btn-outline pull-right",
                        callback: yes_callback
                    },
                    no: {
                        label: "Cancelar",
                        className: "btn-outline pull-left",
                    },
                }
            });
        };

        function setupDatatable(containerName, titulo, color, showButton, buttonData, contratistasContratoRoute, editContratistaRoute, hideIfEmpty) {
            var $box = $.App.Box.init(containerName, titulo, color);
            $.App.Box.addTD(containerName, {'class': 'text-right', text: 'ID'});
            $.App.Box.addTD(containerName, {text: 'Razón Social'});
            $.App.Box.addTD(containerName, {text: 'Contacto'});
            $.App.Box.addTD(containerName, {text: 'Doc. Empresa'});
            $.App.Box.addTD(containerName, {text: 'Doc. Trabajadores'});
            $.App.Box.addTD(containerName, {text: 'Doc. Máquinas'});
            if (showButton) {
                $.App.Box.addTD(containerName, {});
                $.App.Box.addActionButton(containerName, 'add-contratista-wizard', buttonData, 'Añadir', 'plus');
            }

            var columns = [
                { data: 'id', name: 'id', className: 'text-right', cellType: 'th', width: '10px' },
    			{ data: 'razon_social', name: 'razon_social', cellType: 'th' },
                { data: 'contacto', name: 'contacto', orderable: false },
    			{ data: 'status_doc', name: 'status_doc', className: 'text-center vcenter' },
    			{ data: 'status_trabajadores', name: 'status_trabajadores', className: 'text-center vcenter' },
    			{ data: 'status_maquinas', name: 'status_maquinas', className: 'text-center vcenter' },
            ];
            var dt = $.App.DT.set({
                tableName: containerName + '-table',
                columnsDef: columns,
                urlList: contratistasContratoRoute,
                urlEdit: editContratistaRoute,
                addActionColumn: showButton,
                buttonsInActionColumn: 1,
            });
            if (hideIfEmpty) {
                dt.on('draw', function(o, data) {
                    var d = data['json'];
                    if (d && (d.recordsFiltered > 0)) {
                        $box.fadeIn();
                    } else {
                        $box.fadeOut();
                    }
                });
            }
            $('#' + containerName + '-table tbody').on('click', 'a', function(e) {
                e.stopPropagation();
                var a = $(e.currentTarget);
                var id = a.data('id');
                var contratistaId = a.data('contratista');
                if (id != null) {
                    detachContratistaConfirmationDialog((contratistaId != null), function() {
                        var route = _baseRoute + "/contratistas/detach/" + id;
                        if (contratistaId != null) {
                            route += "?c=" + contratistaId;
                        }
                        $.post(route, function(d) {
                            location.reload();
                        });
                    });
                }
            });
        }

        var output = {
            init: function(baseRoute, listaUsuariosEmpresaRoute, contratistasContratoRoute, editContratistaRoute, contratista, canAdd, invitarSubcontratistas) {
                _baseRoute = baseRoute;
                _contratista = contratista;
                _listaUsuariosEmpresaRoute = listaUsuariosEmpresaRoute;
                if (contratistasContratoRoute != null) {
                    setupDatatable('contratistas',
                                   'Contratistas del contrato',
                                   'primary',
                                   canAdd,
                                   null,
                                   contratistasContratoRoute,
                                   editContratistaRoute,
                                   false);
                }
                $('.subcontratistas_box').each(function() {
                     if (editContratistaRoute == null) {
                        return;
                    }

                    var containerName = $(this).attr('id');
                    var id = containerName.replace('subcontratistas_', '');
                    var subcontratistaRoute = contratistasContratoRoute + '?c=' + id;
                    editContratistaRoute = editContratistaRoute.replace('XX', id);
                    var pos = editContratistaRoute.indexOf('?r=');
                    var subcontratistaEditRoute = editContratistaRoute.substr(0, pos) + '/subcontratista/XX' + editContratistaRoute.substr(pos);
                    setupDatatable(containerName,
                                   'Subcontratistas de <strong>' + $(this).data('name') + ' (#' + id + ')</strong>' ,
                                    'warning',
                                    _contratista,
                                    { 'data-contratista': id },
                                    subcontratistaRoute,
                                    subcontratistaEditRoute,
                                    !_contratista);
                });
                $modal.on('show.bs.modal', function (e) {
                    var contratista = $(e.relatedTarget).data('contratista');
                    if (contratista != null) {
                        _addSubcontratistaOf = contratista;
                    }
                    _typeStr = _addSubcontratistaOf != null ? 'Subcontratista' : 'Contratista';
                    resetConSelect(contratista);
                    resetPersonaSelect();
                    setInitialStatus();
                    resetCamposFormulario();
                    $('#contratista_existente').iCheck('check');
                    $('#contratista_id').val(contratista);
                });
                $nextBtn.on('click', function(e) {
                    $page1.hide();
                    $modalTitle.text('Añadir ' + _typeStr + ' - Paso 2/2')
                    if ($newChk.prop('checked')) {
                        $page2a.show();
                    } else {
                        $page2b.show();
                    }
                    $nextBtn.hide();
                    $backBtn.show();
                    $finalBtn.show();
                });
                $backBtn.on('click', function(e) {
                    setInitialStatus();
                });
                $finalBtn.on('click', function (e) {
                    finalButtonClicked(e);
                });
                $('input[type="radio"]').on('ifChecked', function(e) {
                    checkOptionSelected();
                });
                $conSelect.on('change', function(e) {
                    checkOptionSelected();
                    if ($conSelect.val() == null) {
                        resetPersonaSelect();
                    } else {
                        fillPersonaSelect($conSelect.val());
                    }
                });
                if (contratista && !invitarSubcontratistas) {
                    $('#2a_li_3').hide();
                    $('#2a_li_4').hide();
                    $('#2a_li_5').hide();
                    $('#2b_li_2').hide();
                    $('#persona-contacto-group').hide();
                }
            },
        };
        return output;
    }
})(window.jQuery);
