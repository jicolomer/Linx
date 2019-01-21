(function($) {
    "use strict";

    $.getScript('/js/third/stringops.js');

    $.Contratos = function() {
        var _baseRoute = "/contratos";
        var _options = null;

        // *********************************************************************
        // Main Datatable
        // *********************************************************************
        // Confirmation dialog to remove document
        function removeDocumentConfirmationDialog(yes_callback) {
            bootbox.dialog({
                title: "Por favor, confirme",
                message: "<h3>¿Está seguro de querer quitar el documento del contrato?</h3>",
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
        }
        // Row buttons events
        function setupDocumentRowButtons(tableName, dt) {
            $('#' + tableName + ' tbody').on('click', 'a', function(event) {
                event.stopPropagation();
                var a = $(event.currentTarget);
                var action = a.data('action');
                if (action != null) {
                    var id = a.data('id');
                    // Detach
                    if (action == 'remove') {
                        removeDocumentConfirmationDialog(function() {
                            var route = _baseRoute + "/documentacion/detach/" + id;
                            $.post(route, function(res) {
                                if (res.result === 'success') {
                                    $.App.notify.success(res.msg);
                                    dt.ajax.reload();
                                    if (_options.docFaltanteDT != null) {
                                        _options.docFaltanteDT.ajax.reload();
                                    }
                                } else {
                                    $.App.notify.error(res.msg);
                                }
                            }).fail(function(res) {
                                $.App.notify.error(res.status + ' - ' + res.statusText);
                            });
                        });
                    }
                    // Validation
                    if (action == 'validate') {
                        $.DocumentosValidation().showValidationModal(id, dt);
                    }
                }
            });
        }
        // *********************************************************************
        // SELECTION Datatable
        // *********************************************************************
        var _prefixSel = '#adjuntar-doc-modal';
        var $modalSel = $(_prefixSel + '-dialog');
        var $formSel = $(_prefixSel + '-form');
        var $tableSel = $(_prefixSel + '-table');
        var $goButton = $(_prefixSel + '-go-button');
        var _selectionDataUrl = "";
        var _selectionPostUrl = "";
        var _selectionDT = null;

        function enableGoBtn(enable) {
            if (enable) {
                $goButton.addClass('btn-primary');
                $goButton.removeAttr('disabled');
            } else {
                $goButton.removeClass('btn-primary');
                $goButton.attr('disabled', 'disabled');
            }
        }

        function goButtonClicked(filtro, datatable) {
            var formData = {
                'ids': $.App.DT.getSelectedRowsIds($tableSel.attr('id')),
                'filter': filtro
            };
            var url = _baseRoute + '/documentacion/add';
            $.post(url, formData, function(res) {
                if (res.result === 'success') {
                    $.App.notify.success(res.msg);
                } else {
                    $.App.notify.error(res.msg);
                }
            }).fail(function(res) {
                $.App.notify.error(res.status + ' - ' + res.statusText);
            }).always(function() {
                datatable.ajax.reload();
                if (_options.docFaltanteDT != null) {
                    _options.docFaltanteDT.ajax.reload();
                }
                $modalSel.modal('hide');
            });
        }

        // Setup the selection datatable
        function setupSelectionDatatable(event, datatable) {
            var firstTime = (_selectionDT == null);
            enableGoBtn(false);
            if (! firstTime) {
                _selectionDT.rows().deselect();
                _selectionDT.destroy();
            }
            var dataUrl = _baseRoute + '/documentacion/list';
            $('#adjuntar-doc-modal-add-doc-button').show();
            var filtro = $(event.relatedTarget).data('filter');
            if (filtro) {
                dataUrl += '?' + filtro;
                if (filtro.startsWith('td=')) {
                    $('#adjuntar-doc-modal-add-doc-button').hide();
                }
            }
            var columns_collection = [
                { data: 'id', name: 'id', className: 'text-right', cellType: 'th', width: '10px' },
                { data: 'nombre', name: 'nombre', cellType: 'th' },
                { data: 'ambito', name: 'ambito' },
                { data: 'fecha_documento', name: 'fecha_documento' },
                { data: 'status_caducidad', name: 'status_caducidad', className: 'text-center vcenter', orderable: false, searchable: false, width: '50px' },
                { data: 'status_validacion', name: 'status_validacion', className: 'text-center vcenter', orderable: false, searchable: false, width: '50px' },
                { data: 'notas', name: 'notas' },
                { data: 'tags', name: 'tags', orderable: false, searchable: false },
            ];
            _selectionDT = $.App.DT.set({
                tableName: $tableSel.attr('id'),
                columnsDef: columns_collection,
                urlList: dataUrl,
                selectType: true,
            });
            if (firstTime) {
                $tableSel.on('select.dt', function (e, api, type, indexes) {
                    var rows = _selectionDT.rows({selected: true, page: 'all'}).data();
                    enableGoBtn(rows.length > 0);
                });
                $tableSel.on('deselect.dt', function (e, api, type, indexes) {
                    var rows = _selectionDT.rows({selected: true, page: 'all'}).data();
                    enableGoBtn(rows.length > 0);
                });
            }
            $goButton.unbind('click');
            $goButton.on('click', function(event) {
                goButtonClicked(filtro, datatable);
            });
        };

        var output = {
            setupDocFaltante: function(containerName, textTitle, urlFilter, edit) {
                if (edit == null) {
                    edit = true;
                }
                var $box = $.App.Box.init(containerName, 'Documentación faltante ' + textTitle, 'danger');
                $.App.Box.addTD(containerName, {'class': 'text-right', text: 'ID'});
                $.App.Box.addTD(containerName, {text: 'Referencia'});
                $.App.Box.addTD(containerName, {text: 'Nombre'});
                $.App.Box.addTD(containerName, {text: 'Ámbito'});
                $.App.Box.addTD(containerName, {text: 'Caducidad'});
                $.App.Box.addTD(containerName, {text: 'Notas'});
                $.App.Box.addTD(containerName, {text: 'Palabras clave'});
                $.App.Box.addTD(containerName, {text: '¿Obligatorio?'});
                if (edit) {
                    $.App.Box.addTD(containerName, {});
                }
                $('#' + containerName).hide();
                var columns_collection = [
                    { data: 'id', name: 'id', className: 'text-right', cellType: 'th', width: '10px' },
                    { data: 'referencia', name: 'referencia', cellType: 'th' },
                    { data: 'nombre', name: 'nombre', cellType: 'th' },
                    { data: 'ambito', name: 'ambito' },
                    { data: 'tipo_caducidad', name: 'tipo_caducidad' },
                    { data: 'notas', name: 'notas', visible: false },
                    { data: 'tags', name: 'tags', orderable: false, searchable: false },
                    { data: 'obligatorio', name: 'obligatorio', className: 'text-center vcenter', orderable: false, searchable: false },
                ];
                var dt = $.App.DT.set({
                    tableName: containerName + '-table',
                    columnsDef: columns_collection,
                    urlList: _baseRoute + '/documentacion/faltante/data?' + urlFilter,
                    addActionColumn: edit,
                    buttonsInActionColumn: 2,
                    showButtons: false,
                    dtOptions: {
                    	filter: false,
                    	lengthChange: false,
                    	paginate: false,
                    	info: false,
                    },
                });
                dt.on('draw', function() {
                    if (dt.rows().count() == 0) {
                        $box.fadeOut();
                    } else {
                        $box.fadeIn();
                    }
                });
                return dt;
            },
            setupDocContrato: function(options) {
                var defaultOptions = {
                    showAmbito: true,
                    showCaducidad: true,
                    showValidacion: true,
                    buttonsInActionColumn: 2,
                    docFaltanteDT: null,
                }
                _options = mergeObject({}, defaultOptions, options);
                var $box = $.App.Box.init(_options.containerName, 'Documentación ' + _options.textTitle);
                var checkSearchTags = true;
                var columns_collection = [
                    { data: 'id', name: 'id', className: 'text-right', cellType: 'th', width: '10px' },
                    { data: 'nombre', name: 'nombre', cellType: 'th' },
                ];
                $.App.Box.addTD(_options.containerName, {'class': 'text-right', text: 'ID'});
                $.App.Box.addTD(_options.containerName, {text: 'Documento'});
                if (_options.showAmbito) {
                    $.App.Box.addTD(_options.containerName, {text: 'Ámbito'});
                    $.App.Box.addTD(_options.containerName, {text: 'Centro'});
                    columns_collection.push(
                        { data: 'ambito', name: 'ambito' },
                        { data: 'centro', name: 'centro', className: 'text-center vcenter', width: '10px' }
                    );
                }
                $.App.Box.addTD(_options.containerName, {text: 'Fecha Doc.'});
                columns_collection.push(
                    { data: 'fecha_documento', name: 'fecha_documento', className: 'text-center' }
                );
                if (_options.showCaducidad) {
                    $.App.Box.addTD(_options.containerName, {text: 'Estatus Caducidad'});
                    columns_collection.push(
                        { data: 'status_caducidad', name: 'status_caducidad', className: 'text-center vcenter', orderable: false, searchable: false, width: '50px' }
                    );
                }
                if (_options.showValidacion) {
                    $.App.Box.addTD(_options.containerName, {text: '¿Validado?'});
                    columns_collection.push(
                        { data: 'status_validacion', name: 'status_validacion', className: 'text-center vcenter', orderable: false, searchable: false, width: '50px' }
                    );
                }
                $.App.Box.addTD(_options.containerName, {text: 'Notas'});
                $.App.Box.addTD(_options.containerName, {text: 'Palabras clave'});
                $.App.Box.addTD(_options.containerName, {});
                columns_collection.push(
                    { data: 'notas', name: 'notas' },
                    { data: 'tags', name: 'tags' }
                );
                if (_options.edit) {
                    $.App.Box.addActionButton(_options.containerName, 'adjuntar-doc-modal-dialog', { 'data-filter': _options.urlFilter }, 'Adjuntar más', 'exchange');
                } else {
                    $.App.Box.addActionButton(_options.containerName, null, { 'id': _options.containerName+'-download-all-button', 'data-filter': _options.urlFilter }, 'Descargar todo', 'download');
                }
                var url = _baseRoute + '/documentacion/data?' + _options.urlFilter;
                if (! _options.edit) {
                    url += '&noRemove=true';
                }
                var dt = $.App.DT.set({
                    tableName: _options.containerName + '-table',
                    columnsDef: columns_collection,
                    urlList: url,
                    buttonsInActionColumn: _options.buttonsInActionColumn,
                    initComplete: function() {
                        setupDocumentRowButtons(_options.containerName + '-table', dt);
                    },
                });
                $modalSel.on('show.bs.modal', function (event) {
                    setupSelectionDatatable(event, dt);
                });
                if (! _options.edit) {
                    $('#'+_options.containerName+'-download-all-button').on('click', function (e) {
                        var ids = dt.rows().ids().toArray();
                        var title = $(this).closest('.box-header').first().children('h3').text().toSlug();
                        console.log(ids, title);
                        var formData = {
                            'ids': ids,
                            'title': title
                        };
                        window.location = '/api/documentos/zip?title=' + title + '&ids[]=' + ids.join('&ids[]=');
                    });
                }
            },
            getSelectionDatatable: function () {
                return _selectionDT;
            },
            setupTipoContratoSelect: function() {
                $('#tipo_contrato_id').select2({
                    placeholder: $('#tipo_contrato_id').find('option:first').text(),
                    width: '100%',
                    allowClear: true,
                    templateResult: function(data) {
                        var r = data.text.split('|');
                        var html = '<div class="row"><div class="col-md-4"><strong>' + r[0] + '</strong></div>';
                        if (r.length > 1) {
                            html += '<div class="col-md-8"><em>' + r[1] + '</em></div>';
                        }
                        var $result = $(html + '</div>');
                        return $result;
                    },
                    templateSelection: function(data) {
                        var r = data.text.split('|');
                        return $('<strong>' + r[0] + '</strong>');
                    }
                });
            },
        };
        return output;
    };

})(window.jQuery);
