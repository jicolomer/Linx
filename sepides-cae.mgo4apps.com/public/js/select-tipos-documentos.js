(function($) {
    "use strict";

    $.STD = function() {
        var _prefix = '#tipos-documentos-modal';
        var $modal = $(_prefix + '-dialog');
        var $form = $(_prefix + '-form');
        var $table = $(_prefix + '-table');
        var $goButton = $(_prefix + '-go-button');

        var _baseRoute = '';
        var _selectionDatatable = null;
        var _datatable = null;

        function enableGoBtn(enable) {
            if (enable) {
                $goButton.addClass('btn-primary');
                $goButton.removeAttr('disabled');
            } else {
                $goButton.removeClass('btn-primary');
                $goButton.attr('disabled', 'disabled');
            }
        }

        function goButtonClicked(event) {
            var formData = {'ids': $.App.DT.getSelectedRowsIds($table.attr('id')) };
            var url = _baseRoute + '/tipos-documentos/add';
            $.post(url, formData, function(res) {
                if (res.result === 'success') {
                    $.App.notify.success(res.msg);
                }
            }).fail(function(res) {
                $.App.notify.error(res.status + ' - ' + res.statusText);
            }).always(function() {
                _datatable.ajax.reload();
                $modal.modal('hide');
            });
        }
        // SETUP DEFAULT DATATABLE PARA SELECCIONAR TIPOS DE DOCUMENTOS
        function setupSelectionDatatable(selectionTableGetDataUrl) {
            if (selectionTableGetDataUrl == null) {
                selectionTableGetDataUrl = _baseRoute + "/tipos-documentos/data";
            }
            var columns_collection = [
                { data: 'id', name: 'id', className: 'text-right', cellType: 'th', width: '10px' },
                { data: 'referencia', name: 'referencia', cellType: 'th' },
                { data: 'nombre', name: 'nombre', cellType: 'th' },
                { data: 'ambito', name: 'ambito' },
                { data: 'tipo_caducidad', name: 'tipo_caducidad' },
                { data: 'tags', name: 'tags' },
            ];
            _selectionDatatable = $.App.DT.set({
                tableName: $table.attr('id'),
                columnsDef: columns_collection,
                urlList: selectionTableGetDataUrl,
                selectType: true,
            });
            $modal.on('show.bs.modal', function (event) {
                _selectionDatatable.rows().deselect();
                enableGoBtn(false);
            });
            $table.on('select.dt', function (e, api, type, indexes) {
                var rows = _selectionDatatable.rows({selected: true, page: 'all'}).data();
                enableGoBtn(rows.length > 0);;
            });
            $table.on('deselect.dt', function (e, api, type, indexes) {
                var rows = _selectionDatatable.rows({selected: true, page: 'all'}).data();
                enableGoBtn(rows.length > 0);;
            });
            $goButton.on('click', function(event) {
                goButtonClicked(event);
            });
        };

        function setupDatatable(datatableName, datatableGetDataUrl, datatableTipoDocumentoName, active) {
            var columns_collection = [
                { data: 'id', name: 'id', className: 'text-right', cellType: 'th', width: '10px' },
                { data: 'referencia', name: 'referencia', cellType: 'th' },
                { data: 'nombre', name: 'nombre', cellType: 'th' },
                { data: 'ambito', name: 'ambito' },
                { data: 'tipo_caducidad', name: 'tipo_caducidad' },
                { data: 'tags', name: 'tags' },
                { data: 'is_obligatorio', name: 'is_obligatorio', orderable: false, searchable: false, width: '30px', className: 'text-center' },
            ];
            _datatable = $.App.DT.set({
                tableName: datatableName,
                columnsDef: columns_collection,
                urlList: datatableGetDataUrl,
                addActionColumn: active,
                buttonsInActionColumn: 1,
            });

            _datatable.on('draw', function() {
                if (active) {
                    $('.tipo_documento_obligatorio').iCheck({
                        checkboxClass: 'icheckbox_minimal-blue',
                    });
                    $('.tipo_documento_obligatorio').on('ifToggled', function(event) {
                        var check = this;
                        var id = check.id.replace('obligatorio_', '');
                        var checked = $('#' + check.id).prop('checked') ? true : false;
                        var formData = {};
                        formData['t'] = id;
                        formData['o'] = checked;
                        $.post(_baseRoute + '/tipos-documentos/obligatorio', formData, function(data) {
                            if (data.result === 'success') {
                                $.App.notify.success(data.msg);
                            } else {
                                $.App.notify.error(data.msg);
                            }
                        })
                        .always(function () {
                            _datatable.ajax.reload();
                        });
                    });
                } else {
                    $('.tipo_documento_obligatorio').iCheck({
                        checkboxClass: 'icheckbox_minimal-blue',
                        disabledClass: '',
                    });
                    $('.tipo_documento_obligatorio').iCheck('disable');
                }
            });

            if (active) {
                $('#' + datatableName + ' tbody').on('click', 'a', function(event) {
                    event.stopPropagation();
                    var _this = this;
                    var id = $(_this).data('id');
                    if (id != null) {
                        detachDocumentConfirmationDialog(function() {
                            var route = _baseRoute + "/tipos-documentos/detach/" + id;
                            $.post(route, function(res) {
                                if (res.result === 'error') {
                                    $.App.notify.error(res.msg);
                                } else {
                                    $.App.notify.success(res.msg);
                                    _datatable.ajax.reload();
                                }
                            }).fail(function(res) {
                                $.App.notify.error(res.status + ' - ' + res.statusText);
                            });
                        }, datatableTipoDocumentoName);
                    }
                });
            }

            return _datatable;
        };
        // DIALOGO CONFIRMACION QUITAR TIPO DOCUMENTO
        function detachDocumentConfirmationDialog(yes_callback, tipoDocumentoName) {
            if (tipoDocumentoName == null) {
                tipoDocumentoName = 'el Tipo de Documento';
            }
            bootbox.dialog({
                title: "Por favor, confirme",
                message: "<h3>¿Está seguro de querer quitar " + tipoDocumentoName + "?</h3>",
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

        var output = {
            init: function(baseRoute, selectionTableGetDataUrl, datatableName, datatableGetDataUrl, datatableTipoDocumentoName, active) {
                _baseRoute = baseRoute;
                if (active) {
                    setupSelectionDatatable(selectionTableGetDataUrl);
                }
                return setupDatatable(datatableName, datatableGetDataUrl, datatableTipoDocumentoName, active);
            }
        };
        return output;
    }
})(window.jQuery);
