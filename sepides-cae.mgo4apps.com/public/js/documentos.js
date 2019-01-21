(function($) {
    "use strict";

    var _selectize = null;
    var _isNew = true;

    var _datatableVer = null;

    var _prefixDoc = '#documentos-modal';
    var $modalDoc = $(_prefixDoc + '-dialog');
    var $formDoc = $(_prefixDoc + '-form');
    var $titleLabel = $(_prefixDoc + '-title-label');
    var $errorsBox = $(_prefixDoc + '-errors-box');
    var $errorsBox_ul = $(_prefixDoc + '-errors-box ul');
    var $goBtn = $(_prefixDoc + '-go-button');
    var $newVerBtn = $(_prefixDoc + '-new-version-button');
    var $downloadBtn = $(_prefixDoc + '-download-button');

    var $nombre = $('#nombre_documento');
    var $tds = $('#tipo_documento_id');
    var $tdTags = $('#tipo_documento_tags');
    var $tags = $('#tags');
    var $fechaDocumento = $('#fecha_documento');
    var $fechaCaducidad = $('#fecha_caducidad');
    var $caducidadGroup = $('#caducidad_group');

    var _prefixVer = '#versiones-modal';
    var $modalVer = $(_prefixVer + '-dialog');

    var _tags = [];
    var menu_button_pressed = false;

    // TAGS
    $.getScript('/plugins/selectize/selectize.min.js', function () {
        $tags.selectize({
            plugins: ['remove_button'],
            delimiter: ',',
            persist: false,
            valueField: 'tag',
            labelField: 'tag',
            searchField: 'tag',
            options: _tags,
            create: function(input) {
                return {
                    tag: input
                }
            }
        });
        _selectize = $tags[0].selectize;
    });

    $.Documentos = function() {
        var _baseRoute = '';
        var _datatableDoc = null;

        function goButtonClicked(event) {
            event.preventDefault();
            event.stopPropagation();
            resetModalFormErrors();
            var input = $formDoc.serializeArray();
            var data = new FormData();
            var contentType = false;
            $.each(input, function(index, input) {
                data.append(input.name, input.value);
            });
            data.append('tipo_documento_ambito', $('#tipo_documento_ambito').val());
            // Append files to FormData object.
            $.each($formDoc.find('[type=file]'), function(index, input) {
                if (input.files.length == 1) {
                    data.append(input.name, input.files[0]);
                } else if (input.files.length > 1) {
                    data.append(input.name, input.files);
                }
            });
            var url = _baseRoute + '/documentos/add';
            $.ajax({
                type: "POST",
                url: url,
                data: data,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false
            }).success(function(res) {
                if (res.result === 'success') {
                    $.App.notify.success(res.msg);
                    if (_datatableDoc != null) {
                        _datatableDoc.ajax.reload();
                    }
                } else {
                    $.App.notify.error(res.msg);
                }
                $modalDoc.modal('hide');
            }).fail(function(res) {
                if (res.status == 422) {
                    var errors = $.parseJSON(res.responseText);
                    $.each(errors, function(field, message) {
                        var formGroup = $('[name=' + field + ']', $formDoc).closest('.form-group');
                        formGroup.addClass('has-error');
                        $errorsBox_ul.append('<li>'+message+'</li>');
                    });
                    $errorsBox.show();
                } else {
                    $.App.notify.error(res.status + ' - ' + res.statusText);
                    $modalDoc.modal('hide');
                }
            });
        }

        // CADUCIDAD
    	function getCaducidad() {
    		var tipo_documento_id = $tds.val();
    		var fecha_documento = $fechaDocumento.val();

    		$.getJSON('/api/documentos/caducidad?t='+tipo_documento_id+'&f='+fecha_documento, function(d) {
    			if (d) {
                    $('#tipo_documento_ambito').val(d.ambito).trigger('change');
    				showCaducidad(d.fecha_caducidad, d.tipo_caducidad, d.caducidad);
    			}
    		});
    	}

    	function showCaducidad(fecha_caducidad, tipo_caducidad, caducidad) {
    		$fechaCaducidad.val(fecha_caducidad);
    		if (caducidad) {
    			$('#tipo_caducidad_text').text(caducidad);
                $('#tipo_caducidad').val(tipo_caducidad);
    			if (tipo_caducidad == 'V') {
    				$fechaCaducidad.removeAttr('readonly');
    				$fechaCaducidad.datepicker();
    			} else {
    				$fechaCaducidad.attr('readonly', 'readonly');
    				$fechaCaducidad.datepicker('remove', null, null);
    			}
    			if (tipo_caducidad == 'N') {
    				$caducidadGroup.removeClass('required');
    			} else {
    				$caducidadGroup.addClass('required');
    			}
    		}
    	}

        function tdsSelected() {
            changeTipoDocumentoTags();
            getCaducidad();
            if (_isNew) {
                var t = $('#select2-tipo_documento_id-container').attr('title');
                var n = $nombre.val();
                if ((n == "") || (t == n)) {
                    $nombre.val(t);
                }
            }
        }

        function tdsUnselected() {
            $fechaCaducidad.val('');
            $fechaCaducidad.attr('readonly');
            $('#tipo_caducidad_text').text('');
            $('#tipo_caducidad').val(null);
            $caducidadGroup.removeClass('required');
            if (_isNew) {
                $nombre.val("");
            }
        }

        function changeTipoDocumentoTags() {
            var tipo_documento_id = $tds.val();
            $.getJSON('/api/documentos/tipo-documento-tags/' + tipo_documento_id, function(d) {
    			if (d) {
                    // old
                    var old_tags = $tdTags.val().split(',');
                    old_tags.forEach(function(tag) {
                        _selectize.removeItem(tag, true);
                    });
                    var tags = d.tags.split(',');
                    tags.forEach(function(tag) {
                        _selectize.createItem(tag);
                    });
                    $tdTags.val(d.tags);
    			}
    		});
        }

        // ON LOAD MODAL
        function showModal(isNew, tipo, tipo_doc, title) {
            _isNew = isNew;
            if (title == null) {
                title = 'Documento';
            }
            resetModalFormErrors();
            $tds.removeAttr("disabled");
            $nombre.removeAttr("readonly");
            var $inputTipo = $('#tipo_documento_trabajador');
            if ($inputTipo != null) {
                $inputTipo.remove();
            }
            // IS NEW?
            if (_isNew) {
                changeActionForm("new");
                $('#id-group').hide();
                $formDoc[0].reset();
                if ((tipo_doc != null) && (tipo_doc > 0)) {
                    $tds.val(tipo_doc).trigger("change");
                    tdsSelected();
                } else if ($tds != null) {
                    $tds.val(null).trigger('change');
                    tdsUnselected();
                }
                _tags = [];
                if (_selectize != null) {
                    _selectize.clear(true);
                }
                $('#download-file-group').hide();
                $('#file-group').show();
                $newVerBtn.hide();
                $goBtn.html('Guardar');
            } else {
                changeActionForm("update");
                $('#id-group').show();
                $('#download-file-group').show();
                $('#file-group').hide();
                $newVerBtn.show();
                $goBtn.html('Guardar cambios');
            }
            // TITLE
            $titleLabel.text((_isNew? 'Añadir' : 'Modificar') + ' ' + title);
            // TIPO
            if (tipo != null) {
                $formDoc.append('<input type="hidden" name="tipo_documento_trabajador" id="tipo_documento_trabajador" value="'+tipo+'" />');
                toggleHorasFormacion(tipo === 'FOR');
            } else {
                toggleHorasFormacion(false);
            }
        }

        function toggleHorasFormacion(show) {
            if (show) {
                $('#horas_formacion-group').show();
                $('#horas_formacion').attr("disabled", false);
            } else {
                $('#horas_formacion-group').hide();
                $('#horas_formacion').attr("disabled", true);
            }
        }

        function changeActionForm(action) {
            var $inputAction = $('#_action');
            if ($inputAction != null) {
                $inputAction.val(action);
            }
        }

        // Reset Modal Error Box
        function resetModalFormErrors() {
            var $fg = $('#' + $formDoc.attr('id') + ' .form-group');
            $fg.removeClass('has-error');
            $fg.find('.help-block').remove();
            $errorsBox.hide();
            $errorsBox_ul.empty();
        }

        // DIALOGO CONFIRMACION QUITAR DOCUMENTO
        function removeDocumentConfirmationDialog(yes_callback) {
            bootbox.dialog({
                title: "Por favor, confirme",
                message: "<h3>¿Está seguro de querer archivar el documento?</h3>(El documento se almacenará en el <em>Histórico</em>)",
                className: "modal-danger",
                onEscape: function() {},
                buttons: {
                    si: {
                        label: "Sí, quiero archivarlo",
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

        // ROW BUTTONS
        function setupDocumentRowButtons(containerName) {
            $('#' + containerName + ' tbody').on('click', 'a', function(e) {
                e.stopPropagation();
                var a = $(e.currentTarget);
                var action = a.data('action');
                if (action != null) {
                    var id = a.data('id');
                    // Validation
                    if (action == 'validate') {
                        $.DocumentosValidation().showValidationModal(id, _datatableDoc);
                    }
                    if (action == 'remove') {
                        removeDocumentConfirmationDialog(function() {
                            var route = _baseRoute + "/documentos/detach/" + id;
                            $.post(route, function(res) {
                                if (res.result === 'success') {
                                    $.App.notify.success(res.msg);
                                    _datatableDoc.ajax.reload();
                                } else {
                                    $.App.notify.error(res.msg);
                                }
                            }).fail(function(res) {
                                $.App.notify.error(res.status + ' - ' + res.statusText);
                            });
                        });
                    }
                    // Versions
                    if (action == 'versions') {
                        var num_versiones = a.data('versions');
                        if (num_versiones > 0) {
                            setupVersionesDatatable(id);
                            $modalVer.modal('show');
                        }
                    }
                }
            });
            $('#' + containerName + ' tbody').on('click', 'button', function(e) {
                menu_button_pressed = true;
            });
        }

        // EDIT
        function showModalForEdit(documento_id) {
            var url = _baseRoute + "/documentos/" + documento_id + "/data";
            $.getJSON(url, function(d) {
                if (d) {
                    $formDoc[0].reset();
                    $('#id').val(d.id);
                    $nombre.val(d.nombre);
                    $tds.val(d.tipo_documento_id).trigger("change");
                    $fechaDocumento.val(d.fecha_documento);
                    $fechaCaducidad.val(d.fecha_caducidad);
                    $('#horas_formacion').val(d.horas_formacion);
                    $('#notas').val(d.notas);
                    $('#version').val(d.version);
                    $('#filename').val(d.original_filename);
                    $downloadBtn.attr('href', '/api/documentos/download/' + d.id);
                    _selectize.clear(true);
                    for (var i=0,l=d.tags.length; i<l; i++) {
                        _selectize.createItem(d.tags[i].normalized);
                    }
                    getCaducidad();
                    showModal(false, d.tipo_documento_trabajador);
                    $modalDoc.modal('show');
                }
            });
        }

        // NEW VERSION MODAL
        function prepareModalForNewVersion() {
            changeActionForm("version");
            $tds.prop("disabled", true);
            $formDoc.append('<input type="hidden" name="tipo_documento_id" id="_tipo_documento_trabajador" value="' + $tds.val() + '" />');
            $nombre.prop("readonly", true);
            $fechaDocumento.datepicker('update', new Date());
            $('#version').val(getNextVersionNumber());
            $('#download-file-group').hide();
            $('#file-group').show();
            $newVerBtn.hide();
            $goBtn.html('Guardar nueva versión');
            $titleLabel.text('Añadir Nueva Versión del Documento');
        }

        function getNextVersionNumber() {
            var current_version = $('#version').val();
            var current_date = current_version.substr(0, 8);
            var now = new Date();
            var day = now.getDate();
            var month = now.getMonth() + 1;
            var year = now.getFullYear();
            if (day < 10) {
                day = "0" + day;
            }
            if (month < 10) {
                month = "0" + month;
            }
            var new_date = year.toString() + month.toString() + day.toString();
            if (current_date == new_date) {
                var ind = parseInt(current_version.substr(current_version.length - 2)) + 1;
                if (ind < 10) {
                    return new_date + '0' + ind;
                } else {
                    return new_date + ind;
                }
            } else {
                return new_date + "01";
            }
        }

        // SHOW VERSIONS
        function setupVersionesDatatable(documento_id) {
            if (_datatableVer != null) {
                _datatableVer.destroy();
            }
            var columns = [
                { data: 'version', name: 'version', className: 'text-right', cellType: 'th' },
                { data: 'fecha_documento', name: 'fecha_documento', className: 'text-center' },
                { data: 'fecha_caducidad', name: 'fecha_caducidad', className: 'text-center' },
                { data: 'notas', name: 'notas' },
                { data: 'fecha_archivado', name: 'fecha_archivado', className: 'text-center' },
        	];
        	_datatableVer = $.App.DT.set({
                tableName: 'versiones-modal-table',
                columnsDef: columns,
                urlList: '/api/documentos/versiones/' + documento_id,
                buttonsInActionColumn: 1,
                shortList: true,
            });
        }

        var output = {
            init: function(baseRoute) {
                _baseRoute = baseRoute;
                // CADUCIDAD
                $tds.on('select2:select', function(e) {
                    tdsSelected();
                });
                $tds.on('select2:unselect', function(e) {
                    tdsUnselected();
                });
                $fechaDocumento.datepicker().on('changeDate', function(e) {
                    getCaducidad();
                });
                // ON LOAD MODAL
                $modalDoc.on('show.bs.modal', function (event) {
                    var b = $(event.relatedTarget);
                    if (b.hasClass('bootstrap-modal-form-open')) {
                        var isNew = b.data('new');
                        var title = b.data('title');
                        var tipo = b.data('tipo');
                        var tipo_doc = b.data('tipo-doc');
                        showModal(isNew, tipo, tipo_doc, title);
                    }
                });
                $goBtn.on('click', function(event) {
                    goButtonClicked(event);
                });
                $newVerBtn.on('click', function(e) {
                    prepareModalForNewVersion();
                });
            },

            createBox: function(containerName, textTitle, color, border, data) {
                if (textTitle == null) {
                    textTitle = 'Documentos';
                }
                var $box = $.App.Box.init(containerName, textTitle, color, border);
                $.App.Box.addTD(containerName, {'class': 'text-right', text: 'ID'});
                $.App.Box.addTD(containerName, {text: 'Documento'});
                $.App.Box.addTD(containerName, {text: 'Fecha Doc.'});
                $.App.Box.addTD(containerName, {text: 'Estatus Caducidad'});
                $.App.Box.addTD(containerName, {text: '¿Validado?'});
                $.App.Box.addTD(containerName, {text: 'Notas'});
                $.App.Box.addTD(containerName, {text: 'Palabras clave'});
                $.App.Box.addTD(containerName, {});
                var options = { 'data-new': "true" };
                if (data != null) {
                    $.extend(options, data);
                }
                $.App.Box.addActionButton(containerName, 'documentos-modal-dialog', options);
            },

            // SETUP DEFAULT DATATABLE PARA DOCUMENTOS
            setupDatatable: function(tableName, urlList, columnsDef) {
                if (urlList == null) {
                    urlList = _baseRoute + "/documentos/data";
                }
                if (columnsDef == null) {
                    columnsDef = [
        	            { data: 'id', name: 'id', className: 'text-right', cellType: 'th' },
        	            { data: 'nombre', name: 'nombre', cellType: 'th' },
        	            { data: 'fecha_documento', name: 'fecha_documento', className: 'text-center' },
        	            { data: 'status_caducidad', name: 'status_caducidad', className: 'text-center vcenter', orderable: false, searchable: false },
        	            { data: 'status_validacion', name: 'status_validacion', className: 'text-center vcenter', orderable: false, searchable: false },
        	            { data: 'notas', name: 'notas' },
        	            { data: 'tags', name: 'tags' },
        	        ];
                }
                _datatableDoc = $.App.DT.set({
                    tableName: tableName,
                    columnsDef: columnsDef,
                    urlList: urlList,
                    buttonsInActionColumn: 3,
                    initComplete: function() {
                        setupDocumentRowButtons(tableName);
                    }
                })
                // TABLE EVENTS
                $('#' + tableName + ' tbody').on('click', 'tr', function(e) {
                    if (menu_button_pressed) {
                        menu_button_pressed = false;
                        return;
                    }
                    var id = $(this).find("th:first").html();
                    showModalForEdit(id);
                });
                return _datatableDoc;
        	},
            // Base Route
            setBaseRoute: function(baseRoute) {
                _baseRoute = baseRoute;
            },
        };
        return output;
    }
})(window.jQuery);
