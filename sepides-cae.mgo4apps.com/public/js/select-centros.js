(function($) {
    "use strict";
    $.SCT = function() {
        var _baseRoute = '';
        var _prefix = '#centros-modal';
        var $modal = $(_prefix + '-dialog');
        var $form = $(_prefix + '-form');
        var $table = $(_prefix + '-table');
        var $goButton = $(_prefix + '-go-button');
        var _datatable = null;
        var _selectionDatatable = null;

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
            var url = _baseRoute + '/centros/add';
            $.post(url, formData, function(res) {
                if (res.result === 'success') {
                    $.App.notify.success(res.msg);
                } else {
                    $.App.notify.error(res.msg);
                }
            }).fail(function(res) {
                $.App.notify.error(res.status + ' - ' + res.statusText);
            }).always(function() {
                _datatable.ajax.reload();
                $modal.modal('hide');
            });
        }

        function setupSelectionDatatable(getDataUrl) {
            var table_name = $table.attr('id');
            var columns_collection = [
                { data: 'id', name: 'id', className: 'text-right', cellType: 'th', width: '10px' },
                { data: 'nombre', name: 'nombre', cellType: 'th' },
                { data: 'codigo_postal', name: 'codigo_postal' },
                { data: 'municipio', name: 'municipio' },
            ];
            _selectionDatatable = $.App.DT.set({
                tableName: table_name,
                columnsDef: columns_collection,
                urlList: getDataUrl,
                selectType: true,
            });
            $modal.on('show.bs.modal', function (event) {
                _selectionDatatable.rows().deselect();
                enableGoBtn(false);
            });
            $table.on('select.dt', function (e, api, type, indexes) {
                var rows = _selectionDatatable.rows({selected: true, page: 'all'}).data();
                enableGoBtn(rows.length > 0);
            });
            $table.on('deselect.dt', function (e, api, type, indexes) {
                var rows = _selectionDatatable.rows({selected: true, page: 'all'}).data();
                enableGoBtn(rows.length > 0);
            });
            $goButton.on('click', function(event) {
                goButtonClicked(event);
            });
        };

        function detachCentroConfirmationDialog(yes_callback) {
            bootbox.dialog({
                title: "Por favor, confirme",
                message: "<h3>¿Está seguro de querer quitar el Centro de Trabajo del contrato?</h3>",
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
            init: function(baseRoute, centrosContratoRoute, editCentroRoute, listadoCentrosRoute, active) {
                _baseRoute = baseRoute;
                // DATATABLE con centros asociados al contrato
                var columns = [
                    { data: 'id', name: 'id', className: 'text-right', cellType: 'th', width: '10px' },
                    { data: 'nombre', name: 'nombre', cellType: 'th' },
                    { data: 'codigo_postal', name: 'codigo_postal' },
                    { data: 'municipio', name: 'municipio' },
                    { data: 'email_centro', name: 'email_centro', visible: false },
                    { data: 'telefono_centro', name: 'telefono_centro' },
                    { data: 'persona_contacto', name: 'persona_contacto' },
                    { data: 'telefono_contacto', name: 'telefono_contacto' },
                    { data: 'email_contacto', name: 'email_contacto' },
        	    ];
                _datatable = $.App.DT.set({
                    tableName: 'centros-table',
                    columnsDef: columns,
                    urlList: centrosContratoRoute,
                    urlEdit: editCentroRoute,
                    addActionColumn: active,
                    buttonsInActionColumn: 1,
                });
                if (active) {
                    setupSelectionDatatable(listadoCentrosRoute);
                    $('#centros-table tbody').on('click', 'a', function(event) {
                        event.stopPropagation();
                        var a = $(event.currentTarget);
                        var id = a.data('id');
                        if (id != null) {
                            detachCentroConfirmationDialog(function() {
                                var route = _baseRoute + "/centros/detach/" + id;
                                $.post(route, function(res) {
                                    if (res.result === 'success') {
                                        $.App.notify.success(res.msg);
                                        _datatable.ajax.reload();
                                    } else {
                                        $.App.notify.error(res.msg);
                                    }
                                }).fail(function(res) {
                                    $.App.notify.error(res.status + ' - ' + res.statusText);
                                });
                            });
                        }
                    });
                }

                return _datatable;
            },
        };
        return output;
    }
})(window.jQuery);
