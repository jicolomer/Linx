(function($) {
    "use strict";

    $.AddTrabajadoresMaquinas = function() {
        var _options = {};
        var _currentPage = 1;
        var _typeShown = 0;
        var _selectionDatatables = [];
        var _prefix = "#add-trabajadores-maquinas-wizard";
        var $modal = $(_prefix);
        var $form = $(_prefix + '-form');
        var $modalTitle = $(_prefix + '-title');
        var $errors = $(_prefix + '-errors-box');
        var $errors_ul = $(_prefix + '-errors-box ul');

        var $page1 = $(_prefix + '-page-1');
        var $page2 = $(_prefix + '-page-2');
        var $page3 = $(_prefix + '-page-3');
        var $pages = [];

        var $tableTrabajadores = $(_prefix + '-trabajadores-table');
        var $tableMaquinas = $(_prefix + '-maquinas-table');

        var $nextBtn = $(_prefix + '-next-button');
        var $backBtn = $(_prefix + '-back-button');
        var $finalBtn = $(_prefix + '-final-button');

        var $fechaInicio = $('#fecha_inicio');
        var $fechaFinal = $('#fecha_final');

        var $entreSemanaBtn = $('#entre_semana_button');
        var $findeSemanaBtn = $('#finde_semana_button');
        var $todaSemanaBtn = $('#toda_semana_button');
        var $limpiarSemanaBtn = $('#limpiar_semana_button');

        var $lunes = $('#lunes');
        var $martes = $('#martes');
        var $miercoles = $('#miercoles');
        var $jueves = $('#jueves');
        var $viernes = $('#viernes');
        var $sabado = $('#sabado');
        var $domingo = $('#domingo');
        var _entreSemana = [ $lunes, $martes, $miercoles, $jueves, $viernes ];
        var _findeSemana = [ $sabado, $domingo ];

        String.prototype.capitalizeFirstLetter = function() {
            return this.charAt(0).toUpperCase() + this.slice(1);
        }

        function setInitialStatus() {
            resetErrors();
            for (var i = 1; i < $pages.length; i++) {
                $pages[i].hide();
            }
            setupCurrentPage(1);
            if (_options.itemId == null) {
                if (_typeShown == 0) {
                    $tableTrabajadores.show();
                    $tableMaquinas.hide();
                } else {
                    $tableTrabajadores.hide();
                    $tableMaquinas.show();
                }
            } else {
                $tableTrabajadores.hide();
                $tableMaquinas.hide();
            }
            resetCamposFormulario();
        }

        function setupCurrentPage(currentPage) {
            if (_options.itemId != null) {
                $modalTitle.html('Asignar a <strong>' + _options.itemName + '</strong> - Paso ' + currentPage + '/' + $pages.length);
            } else {
                $modalTitle.text('Asignar ' + _options.config[_typeShown].pluralName.capitalizeFirstLetter() + ' - Paso ' + currentPage + '/' + $pages.length);
            }
            if (currentPage == 1) {
                $backBtn.hide();
                $nextBtn.show();
                enableNextBtn(false);
                $finalBtn.hide();
                if (_options.itemId != null) {
                    allowNextStep('select-centros-table');
                } else {
                    allowNextStep('select-' + _options.config[_typeShown].containerName + '-table');
                }
            } else if (currentPage == $pages.length) {
                $backBtn.show();
                $nextBtn.hide();
                $finalBtn.show();
                $fechaInicio.focus();
            } else {
                $backBtn.show();
                $nextBtn.show();
                allowNextStep('select-centros-table');
                $finalBtn.hide();
            }
            $pages[_currentPage-1].hide();
            $pages[currentPage-1].show();
            _currentPage = currentPage;
        }

        function resetErrors() {
            $errors.hide();
            $errors_ul.empty();
            $form.find('.form-group').removeClass('has-error');
        }

        function resetCamposFormulario() {
            for (var i = 0; i < _selectionDatatables.length; i++) {
                _selectionDatatables[i].rows().deselect();
            }
            $fechaInicio.val(_options.fechaInicio);
            $fechaFinal.val(_options.fechaFin);
            checkDays(_entreSemana.concat(_findeSemana), false);
            checkDays(_entreSemana, true);
        }

        function enableNextBtn(enable) {
            if (enable) {
                $nextBtn.addClass('btn-primary');
                $nextBtn.removeAttr('disabled');
            } else {
                $nextBtn.removeClass('btn-primary');
                $nextBtn.attr('disabled', 'disabled');
            }
        }

        function allowNextStep(tableName) {
            var rowsSelected = $('#' + tableName + ' > tbody > tr.selected');
            enableNextBtn(rowsSelected.length > 0);
        }

        function nextBtnClicked() {
            if (_currentPage < 3) {
                setupCurrentPage(_currentPage + 1);
            }
        }

        function backBtnClicked() {
            if (_currentPage > 1) {
                setupCurrentPage(_currentPage - 1);
            }
        }

        function finalButtonClicked() {
            modalLoading.init(true);
            var formData = {};
            $('input[name="' + _options.config[_typeShown == 0 ? 1 : 0].containerName + '_ids"]').remove();
            $.App.DT.addSelectedRowsInput('select-' + _options.config[_typeShown].containerName + '-table', $form, _options.config[_typeShown].containerName + '_ids');
            $.App.DT.addSelectedRowsInput('select-centros-table', $form, 'centros_ids');
            $form.find('input[type=checkbox]').each(function() {
                if ($(this).is(':checked')) {
                    formData[$(this).attr('name')] = true;
                }
            });
            $form.find('input[type=text], input[type=hidden]').each(function() {
                formData[$(this).attr('name')] = $(this).val();
            });
            var url = '/contratos/trabajadores-maquinas/add';
            if (_options.itemId != null) {
                formData[_options.config[_typeShown].pluralName + '_ids'] = _options.itemId;
                url = '/contratos/trabajador-maquina/add';
            }
            resetErrors();
            $.post(url, formData, function(res) {
                $modal.modal('hide');
                if (res.result === 'success') {
                    $.App.notify.success(res.msg);
                    _options.config[_typeShown].datatable.ajax.reload();
                } else {
                    $.App.notify.error(res.msg);
                }
            }).fail(function(res) {
                console.log(res);
                if (res.status == 422) {
                    var errors = $.parseJSON(res.responseText)['errors'];
                    $.each(errors, function(index, value) {
                        if (index == 'lunes') {
                            $errors_ul.append('<li>Debe seleccionar al menos un <strong>día de trabajo</strong>.</li>');
                        } else if (index == 'martes' || index == 'miercoles' || index == 'jueves' ||
                                   index == 'viernes' || index == 'sabado' || index == 'domingo') {
                            // Nothing
                        } else {
                            $form.find('.form-group').each(function() {
                                var $field = $(this).find('#' + index);
                                if ($field.length) {
                                    $(this).addClass('has-error');
                                }
                            });
                            $errors_ul.append('<li>' + value + '</li>');
                        }
                    });
                    $errors.show();
                } else {
                    $modal.modal('hide');
                    $.App.notify.error(res.status + ' - ' + res.statusText);
                }
            }).always(function() {
                modalLoading.init(false);
            });
        }

        function checkDays(daysArray, checked) {
            daysArray.forEach(function ($day) {
                if (checked == null) {
                    $day.iCheck('toggle');
                } else {
                    $day.iCheck(checked ? 'check' : 'uncheck');
                }
            });
        }

        function detachConfirmationDialog(yes_callback) {
            var config = _options.config[_typeShown];
            bootbox.dialog({
                title: "Por favor, confirme",
                message: "<h3>¿Está seguro de querer quitar " + (_typeShown == 0 ? "el " : "la ") + config.singleName +
                         " del contrato?</h3><br />(También se quitarán sus documentos del contrato si no se necesitan.)",
                className: "modal-danger",
                onEscape: function() {},
                buttons: {
                    si: {
                        label: "Sí, quiero quitarl" + config.genderLetter,
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

        function handleAccess($btn, button) {
            var tipo = $btn.data('tipo');
            var itemId = $btn.data('id');
            var centroId = $btn.data('centro');
            var fechaStr = null;
            var fecha = $btn.data('fecha');
            if (fecha != null) {
                fecha = fecha.toString();
                fechaStr = fecha.substr(6) + '/' + fecha.substr(4,2) + '/' + fecha.substr(0,4);
            }
            var letter = _options.config[tipo].genderLetter;
            var singular = _options.config[tipo].singleName;
            var plural = _options.config[tipo].pluralName;
            var dlgClass = '';
            var sentence = '';
            var details = '';
            var verb = 'dárselo';
            var title = 'Por favor, confirme'
            switch (button) {
                case 'unlockOK':
                    dlgClass = 'modal-success';
                    sentence = '<strong>dar permiso</strong> a tod' + letter + 's l' + letter + 's ' + plural +
                               ' con documentación correcta';
                    break;
                case 'unlockAll':
                    dlgClass = 'modal-warning';
                    sentence = '<strong>dar permiso</strong> a tod' + letter + 's l' + letter + 's ' + plural +
                               ' independientemente del estado de su documentación';
                    break;
                case 'lockAll':
                    title = 'Indique el motivo para quitar el permiso a <strong>tod' + letter + 's</strong> l'  + letter + 's ' + plural;
                    dlgClass = 'modal-danger';
                    verb = 'quitárselo';
                    break;
                case 'unlock':
                    dlgClass = 'modal-success';
                    sentence = '<strong>dar permiso de acceso</strong> a' + (tipo==0 ? 'l ' : ' la ') + singular + ' #' + itemId;
                    details = '(Para el <strong>centro #' + centroId + '</strong> e <strong>inicio de trabajos el ' + fechaStr + '</strong>)';
                    break;
                case 'lock':
                    title = 'Indique el motivo para quitar el permiso a' + (tipo==0 ? 'l ' : ' la ') + singular + ' #' + itemId;
                    dlgClass = 'modal-danger';
                    details = '(Para el <strong>centro #' + centroId + '</strong> e <strong>inicio de trabajos el ' + fechaStr + '</strong>)';
                    verb = 'quitárselo';
                    break;
            }
            var dlgOptions = {
                title: title,
                message: "<h3>¿Está seguro de querer " + sentence +
                         "?</h3><br />" + details,
                className: dlgClass,
                onEscape: true,
                backdrop: true,
            };
            if (button == 'lock' || button == 'lockAll') {
                dlgOptions['inputType'] = 'textarea';
                dlgOptions['callback'] = function(result) {
                    if (result) {
                        sendAccessRequest(button, tipo, itemId, centroId, fecha, result);
                    }
                };
                dlgOptions['buttons'] = {
                    confirm: {
                        label: "<strong>Quitar</strong> permiso",
                        className: "btn-outline pull-right",
                    },
                    cancel: {
                        label: "Cancelar",
                        className: "btn-outline pull-left",
                    },
                };
                bootbox.prompt(dlgOptions);
            } else {
                dlgOptions['buttons'] = {
                    si: {
                        label: "<strong>Sí, quiero " + verb + '</strong>',
                        className: "btn-outline pull-right",
                        callback: function() {
                            sendAccessRequest(button, tipo, itemId, centroId, fecha, null);
                        }
                    },
                    no: {
                        label: "Cancelar",
                        className: "btn-outline pull-left",
                    },
                };
                bootbox.dialog(dlgOptions);
            }
        }

        function sendAccessRequest(button, tipo, itemId, centroId, fecha, motivo) {
            var url = '/contratos/trabajadores-maquinas/access';
            var formData = {
                boton: button,
                tipo: tipo,
            }
            if (itemId != null) {
                url = '/contratos/trabajador-maquina/access';
                formData['item_id'] = itemId;
                formData['centro_id'] = centroId;
                formData['fecha_inicio'] = fecha;
            }
            if (motivo != null) {
                formData['motivo'] = motivo;
            }
            $.post(url, formData, function(res) {
                console.log(res);
                if (res.result === 'error') {
                    $.App.notify.error(res.msg);
                } else {
                    $.App.notify.success(res.msg);
                    _options.config[tipo].datatable.ajax.reload();
                }
            }).fail(function(res) {
                $.App.notify.error(res.status + ' - ' + res.statusText);
            });
        }

        function setupDatatable(tipo) {
            var containerName = _options.config[tipo].containerName;
            var titulo = _options.config[tipo].pluralName.capitalizeFirstLetter() + ' asociad' + _options.config[tipo].genderLetter + 's al contrato';
            var actionColumnWidth = (3 * 35);
            var color = 'primary';
            var $box = $.App.Box.init(containerName, titulo, color);

            $.App.Box.addTD(containerName, {'class': 'text-right', text: 'Centro'});
            $.App.Box.addTD(containerName, {'class': 'text-center', text: 'Fecha Inicio'});
            $.App.Box.addTD(containerName, {'class': 'text-center', text: 'Fecha Fin'});
            $.App.Box.addTD(containerName, {text: _options.config[tipo].singleName.capitalizeFirstLetter()});
            $.App.Box.addTD(containerName, {text: 'Días Trabajo'});
            $.App.Box.addTD(containerName, {'class': 'text-center', text: 'Estatus Doc.'});
            $.App.Box.addTD(containerName, {'class': 'text-center', text: '¿Permiso?'});
            $.App.Box.addTD(containerName, {});
            if (_options.allowAdd) {
                $.App.Box.addActionButton(containerName, _prefix.slice(1), { 'data-tipo': tipo }, 'Asignar', 'plus');
            }

            if (_options.updateAccess) {
                actionColumnWidth = (4 * 35);
                $box.append(
                    '<div class="box-footer">' +
                        '<button data-tipo=' + tipo + ' class="lockAllBtn btn btn-danger pull-right left-margin"><i class="fa fa-lock"></i> &nbsp; Quitar permiso a tod' + _options.config[tipo].genderLetter + 's</button>' +
                        '<button data-tipo=' + tipo + ' class="unlockAllBtn btn btn-warning pull-right left-margin"><i class="fa fa-unlock-alt"></i> &nbsp; Dar permiso a tod' + _options.config[tipo].genderLetter + 's</button>' +
                        '<button data-tipo=' + tipo + ' class="unlockAllOKBtn btn btn-success pull-right left-margin"><i class="fa fa-unlock"></i> &nbsp; Dar permiso a correct' + _options.config[tipo].genderLetter + 's</button>' +
                    '</div>'
                );
                $('.lockAllBtn').unbind('click');
                $('.lockAllBtn').on('click', function() {
                    handleAccess($(this), 'lockAll');
                });
                $('.unlockAllBtn').unbind('click');
                $('.unlockAllBtn').on('click', function() {
                    handleAccess($(this), 'unlockAll');
                });
                $('.unlockAllOKBtn').unbind('click');
                $('.unlockAllOKBtn').on('click', function() {
                    handleAccess($(this), 'unlockOK');
                });
            }

            var columns = [
                { data: 'centro', name: 'centro', className: 'text-right', cellType: 'th', width: '10px', orderData: 8 },
                { data: 'fecha_inicio', name: 'fecha_inicio', className: 'text-center', orderData: 9 },
                { data: 'fecha_fin', name: 'fecha_fin', className: 'text-center', orderData: 10 },
                { data: _options.config[tipo].singleName.replace('á', 'a'), name: _options.config[tipo].singleName.replace('á', 'a'), cellType: 'th' },
                { data: 'dias_trabajo', name: 'dias_trabajo'},
    			{ data: 'status_doc', name: 'status_doc', className: 'text-center vcenter' },
    			{ data: 'status_permiso', name: 'status_permiso', className: 'text-center vcenter' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false, width: actionColumnWidth+'px', className: 'hide-in-colvis' },
                { data: 'centro_id', name: 'centro_id', visible: false, className: 'hide-in-colvis' },
                { data: 'fecha_inicio_trabajos', name: 'fecha_inicio_trabajos', visible: false, className: 'hide-in-colvis' },
                { data: 'fecha_fin_trabajos', name: 'fecha_fin_trabajos', visible: false, className: 'hide-in-colvis' },
            ];
            var dt = $.App.DT.set({
                tableName: containerName + '-table',
                columnsDef: columns,
                urlList: _options.config[tipo].datatableDataUrl,
                addActionColumn: false,
            });

            if (_options.updateAccess) {
                dt.on('draw', function() {
                    $('.unlockBtn').unbind('click');
                    $('.unlockBtn').on('click', function() {
                        handleAccess($(this), 'unlock');
                    });
                    $('.lockBtn').unbind('click');
                    $('.lockBtn').on('click', function() {
                        handleAccess($(this), 'lock');
                    });
                });
            }

            $('#' + containerName + '-table tbody').on('click', '.detach-button', function(e) {
                e.preventDefault();
                var $a = $(e.currentTarget);
                var route = $a.attr('href');
                detachConfirmationDialog(function() {
                    $.post(route, function(res) {
                        console.log(res);
                        if (res.result === 'error') {
                            $.App.notify.error(res.msg);
                        } else {
                            $.App.notify.success(res.msg);
                            dt.ajax.reload();
                        }
                    }).fail(function(res) {
                        $.App.notify.error(res.status + ' - ' + res.statusText);
                    });
                });
            });

            return dt;
        }

        function setupSelectionDatatable(table) {
            var name = '';
            var columns_collection = [];
            var dataUrl = '';

            switch (table) {
                case 0:
                    name = _options.config[table].containerName;
                    columns_collection = [
                        { data: 'id', name: 'trabajadores.id', className: 'text-right', cellType: 'th', width: '10px' },
                        { data: 'apellidos', name: 'trabajadores.apellidos', cellType: 'th' },
                        { data: 'nombre', name: 'trabajadores.nombre', cellType: 'th', width: '30px' },
                        { data: 'nif', name: 'trabajadores.nif' },
                        { data: 'puesto', name: 'trabajadores.puesto' },
                        { data: 'status_formacion', name: 'status_formacion', orderable: false, searchable: false, width: '10px', className: 'text-center' },
                        { data: 'status_informacion', name: 'status_informacion', orderable: false, searchable: false, width: '10px', className: 'text-center' },
                        { data: 'status_epis', name: 'status_epis', orderable: false, searchable: false, width: '10px', className: 'text-center' },
                        { data: 'status_salud', name: 'status_salud', orderable: false, searchable: false, width: '10px', className: 'text-center' },
                        { data: 'status_otros', name: 'status_otros', orderable: false, searchable: false, width: '10px', className: 'text-center' },
                    ];
                    dataUrl = _options.config[table].selectDataUrl;
                    break;
                case 1:
                    name = _options.config[table].containerName;
                    columns_collection = [
                        { data: 'id', name: 'maquinas.id', className: 'text-right', cellType: 'th', width: '10px' },
                        { data: 'tipo', name: 'tipo', cellType: 'th' },
                        { data: 'nombre', name: 'maquinas.nombre', cellType: 'th' },
                        { data: 'matricula', name: 'matricula', cellType: 'th' },
                        { data: 'marca', name: 'marca' },
                        { data: 'modelo', name: 'modelo' },
                        { data: 'documentacion', name: 'documentacion', orderable: false, searchable: false, width: '10px', className: 'text-center' },
                    ];
                    dataUrl = _options.config[table].selectDataUrl;
                    break;
                case 2:
                    name = 'centros';
                    columns_collection = [
                        { data: 'id', name: 'id', className: 'text-right', cellType: 'th', width: '10px' },
                        { data: 'nombre', name: 'nombre', cellType: 'th' },
                        { data: 'codigo_postal', name: 'codigo_postal' },
                        { data: 'municipio', name: 'municipio' },
                    ];
                    dataUrl = _options.selectCentrosUrl;
                    break;
            }

            var dt = $.App.DT.set({
                tableName: 'select-' + name + '-table',
                columnsDef: columns_collection,
                urlList: dataUrl,
                selectType: true,
            });

            $('#select-' + name + '-table').on('select.dt', function (e, api, type, indexes) {
                allowNextStep('select-' + name + '-table');
            });
            $('#select-' + name + '-table').on('deselect.dt', function (e, api, type, indexes) {
                allowNextStep('select-' + name + '-table');
            });
            $entreSemanaBtn.on('click', function() {
                checkDays(_entreSemana);
            });
            $findeSemanaBtn.on('click', function() {
                checkDays(_findeSemana);
            });
            $todaSemanaBtn.on('click', function() {
                checkDays(_entreSemana.concat(_findeSemana), true);
            });
            $limpiarSemanaBtn.on('click', function() {
                checkDays(_entreSemana.concat(_findeSemana), false);
            });
            return dt;
        }

        var output = {
            init: function(options) {
                _options = options;
                _options.config = [{
                        containerName: 'trabajadores',
                        singleName: 'trabajador',
                        pluralName: 'trabajadores',
                        genderLetter: 'o',
                        datatableDataUrl: _options.trabajadoresDataUrl,
                        selectDataUrl: _options.selectTrabajadoresUrl,
                        datatable: null,
                    },{
                        containerName: 'maquinas',
                        singleName: 'máquina',
                        pluralName: 'máquinas',
                        genderLetter: 'a',
                        datatableDataUrl: _options.maquinasDataUrl,
                        selectDataUrl: _options.selectMaquinasUrl,
                        datatable: null,
                }];
                if (_options.trabajadoresDataUrl != null) {
                    _options.config[0].datatable = setupDatatable(0);
                }
                if (_options.maquinasDataUrl != null) {
                    _options.config[1].datatable = setupDatatable(1);
                }
                if (_options.itemId != null) {
                    $pages = [ $page2, $page3 ];
                    _selectionDatatables = [
                        setupSelectionDatatable(2)
                    ];
                } else {
                    $pages = [ $page1, $page2, $page3 ];
                    _selectionDatatables = [
                        setupSelectionDatatable(0),
                        setupSelectionDatatable(1),
                        setupSelectionDatatable(2)
                    ];
                }
                $modal.on('show.bs.modal', function (e) {
                    var b = $(e.relatedTarget);
                    if (b.hasClass('bootstrap-modal-form-open')) {
                        _typeShown = $(e.relatedTarget).data('tipo');
                        setInitialStatus();
                    }
                });
                $nextBtn.on('click', function(e) {
                    nextBtnClicked();
                });
                $backBtn.on('click', function(e) {
                    backBtnClicked();
                });
                $finalBtn.on('click', function(e) {
                    finalButtonClicked();
                });
            },
        };
        return output;
    }
})(window.jQuery);
